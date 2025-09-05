<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\AnalysisController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;

class QuotationController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }
    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class);
    }
    public function index()
    {
        $response = $this->api->get('quotations');
        $quotations = $response->successful() ? $response->json() : [];

        return view('quotations.index', compact('quotations'));
    }
    
    public function table(Request $request)
    {
        // Get filtered/ paginated quotations as per request inputs
        $quotations = Quotation::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $quotations->where('id', 'like', "%$q%");
            // add other filters if needed
        }

        if ($request->filled('status')) {
            $quotations->where('status', $request->input('status'));
        }

        $quotations = $quotations->paginate(10);

        return view('quotations.partials.table', compact('quotations'));
    }
    public function create()
        {
            $patients = $this->api->get('patients')->json();
            $analyses = $this->api->get('analyses')->json();
            $agreements = $this->api->get('agreements', ['status' => 'active'])->json();

            return view('quotations.create', [
                'patients' => $patients,
                'analyses' => $analyses,
                'agreements' => $agreements,
            ]);
        }
        public function store(Request $request)
        {
            // --- 1. Validation ---
            $validator = Validator::make($request->all(), [
                    'patient_id' => 'required|integer',
                    'items' => 'required|array|min:1',
                    'items.*.analysis_id' => 'required|integer',
                    'items.*.price' => 'required|numeric|min:0',
                    'payment.amount' => 'nullable|numeric|min:0',
                    'payment.method' => 'nullable|string|max:50',
                    'payment.notes'  => 'nullable|string|max:255',
                    'payment.amount_received' => 'nullable|numeric|min:0',
                ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = Session::get('user'); // ✅ already stored at login
            if (!$user || !isset($user['id'])) {
                \Log::error("❌ No FastAPI user found in session.");
                return back()->with('error', 'User session expired, please log in again.');
            }

            // --- 2. Prepare quotation payload ---
            $quotationData = [
                'patient_id' => (int) $request->patient_id,
                'status' => 'draft',
                'agreement_id' => $request->agreement_id ? (int) $request->agreement_id : null,
                'items' => array_values(array_map(function($item) {
                    return [
                        'analysis_id' => (int) $item['analysis_id'],
                        'price' => (float) $item['price'],
                    ];
                }, $request->items)),
            ];

            // --- 3. Add payment if provided ---
            if ($request->filled('payment.method')) {
                $quotationData['payment'] = [
                    'amount'          => (float) $request->input('payment.amount'), // net total
                    'method'          => $request->input('payment.method'),
                    'notes'           => $request->input('payment.notes'),
                    'amount_received' => $request->input('payment.amount_received'),
                    'change_given'    => $request->input('payment.change_given'),
                    'user_id'         => $user['id'], // from session
                ];
            }
   
            // --- 4. Logging ---
            \Log::info('📤 Sending quotation payload to FastAPI', $quotationData);

            // --- 5. Create quotation (with optional payment) ---
            $quotationResponse = $this->api->post('quotations', $quotationData);

            if (!$quotationResponse->successful()) {
                \Log::error('❌ Failed to create quotation: ' . $quotationResponse->body());
                return back()->with('error', 'Failed to create quotation.')->withInput();
            }

            $quotation = $quotationResponse->json();
            $quotationId = $quotation['id'] ?? null;

            \Log::info("✅ Quotation created successfully with ID: {$quotationId}");

            return redirect()->route('quotations.index')
                ->with('success', 'Quotation (and payment if provided) created successfully.');
        }

    public function show($id)
    {
        $response = $this->api->get("quotations/{$id}");

        if (!$response->successful()) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }

        $quotation = $response->json();
        return view('quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $quotation = $this->api->get("quotations/{$id}")->json();
        $patients = $this->api->get('patients')->json();
        $analyses = $this->api->get('analyses')->json();
        $agreements = $this->api->get('agreements', ['status' => 'active'])->json();
        if (!$quotation) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }
        return view('quotations.edit', compact('quotation', 'patients', 'analyses', 'agreements'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'analyses' => 'required|array|min:1',
            'analyses.*.analysis_id' => 'required|integer',
            'analyses.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,confirmed,converted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'patient_id' => $request->patient_id,
            'status' => $request->status,
            'total' => $request->total,
            'analyses' => $request->analyses,
        ];

        $response = $this->api->put("quotations/{$id}", $data);

        return $response->successful()
            ? redirect()->route('quotations.index')->with('success', 'Quotation updated.')
            : back()->with('error', 'Failed to update quotation.')->withInput();
    }

    public function destroy($id)
    {
        $response = $this->api->delete("quotations/{$id}");

        return $response->successful()
            ? redirect()->route('quotations.index')->with('success', 'Quotation deleted.')
            : back()->with('error', 'Failed to delete quotation.');
    }

    // Autocomplete APIs
    public function searchPatients(Request $request)
    {
        $q = $request->get('q', '');
        $response = $this->api->get('patients/search', ['q' => $q]);
        return $response->successful() ? $response->json() : [];
    }

    public function searchAnalyses(Request $request)
    {
        $q = $request->get('q', '');
        $response = $this->api->get('analyses/search', ['q' => $q]);
        return $response->successful() ? $response->json() : [];
    }

    public function quotationsTable(Request $request)
    {
        $params = $request->only(['q', 'status']);
        $response = $this->api->get('quotations/table', $params);

        if (!$response->successful()) {
            return response('Failed to load quotations.', 500);
        }

        $quotations = $response->json();
        return view('quotations.partials.quotations_table', compact('quotations'));
    }
    public function convert($id)
    {
        // Call the new FastAPI convert route
        $response = $this->api->put("quotations/{$id}/convert", []);

        if (!$response->successful()) {
            return redirect()
                ->route('quotations.show', $id)
                ->with('error', 'Failed to convert quotation.');
        }

        return redirect()
            ->route('quotations.show', $id)
            ->with('success', 'Quotation converted to visit successfully.');
    }

    // Download quotation as PDF
    public function download($id)
    {
        $response = $this->api->get("quotations/{$id}");
        if (!$response->successful()) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }

        $quotation = $response->json();

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'));

        $fileName = "Facture N° {$quotation['id']} - Dossier {$quotation['patient']['file_number']}.pdf";
        
        return $pdf->download($fileName);
    }
}
