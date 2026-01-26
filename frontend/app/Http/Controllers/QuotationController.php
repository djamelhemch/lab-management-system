<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\AnalysisController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }
    
    
    private function buildQuotationParams(Request $request)
    {
        return array_filter([
            'q'        => $request->get('q'),
            'status'   => $request->get('status'),
            'page'     => $request->get('page', 1),
            'limit'    => $request->get('limit', 10),

            // ✅ NEW — forward real sorting
            'sort_by'  => $request->get('sort_by'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ]);
    }

    public function index(Request $request)
    {
        $params = array_filter([
            'q'        => $request->get('q'),
            'status'   => $request->get('status'),
            'page'     => $request->get('page', 1),
            'limit'    => $request->get('limit', 10),
            'sort_by'  => $request->get('sort_by', 'date'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ]);

        $response = $this->api->get('/quotations', $params);

        $quotations = $response->ok()
            ? $response->json()
            : [
                'items'     => [],
                'total'     => 0,
                'page'      => 1,
                'last_page' => 1,
            ];

        return view('quotations.index', compact('quotations'));
    }

    public function table(Request $request)
    {
        $params = $this->buildQuotationParams($request);

        $response = $this->api->get('/quotations', $params);
        $quotations = $response->ok() ? $response->json() : [
            'items' => [], 'total' => 0, 'page' => 1, 'last_page' => 1,
        ];

        return view('quotations.partials.table', compact('quotations'));
    }

    public function create()
    {
        $patients   = $this->api->get('patients')->json();
        $analyses   = $this->api->get('analyses', ['is_active' => true])->json();
        $agreements = $this->api->get('agreements', ['status' => 'active'])->json();
        $doctors    = $this->api->get('doctors')->json() ?? [];

        // TODAY'S QUOTATIONS
        try {
            $todayResponse = $this->api->get('/quotations/today', ['limit' => 50]);
            Log::info("Today quotations API called", [
                'status' => $todayResponse->status(),
                'body' => $todayResponse->body()
            ]);

            $todayVisits = collect($todayResponse->json())->map(function ($patient) {
                $quotations = collect($patient['quotations'] ?? []);

                if ($quotations->isEmpty()) {
                    return null;
                }

                $latest = $quotations->first();

                return [
                    'patient_id' => $patient['patient_id'] ?? null,
                    'patient_first_name' => $patient['first_name'] ?? '',
                    'patient_last_name' => $patient['last_name'] ?? '',
                    'file_number' => $patient['file_number'] ?? null,
                    'visit_date' => \Carbon\Carbon::parse($latest['created_at'])->format('Y-m-d'),
                    'visit_time' => \Carbon\Carbon::parse($latest['created_at'])->format('H:i:s'),
                    'quotations_count' => $quotations->count(),
                ];
            })->filter();

            Log::info("Today visits processed", ['count' => $todayVisits->count()]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch today's quotations", [
                'error' => $e->getMessage()
            ]);
            $todayVisits = collect();
        }

        return view('quotations.create', compact('patients', 'analyses', 'agreements', 'doctors', 'todayVisits'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'              => 'required|integer',
            'items'                   => 'required|array|min:1',
            'items.*.analysis_id'     => 'required|integer',
            'items.*.price'           => 'required|numeric|min:0',
            'payment.amount'          => 'nullable|numeric|min:0',
            'payment.method'          => 'nullable|string|max:50',
            'payment.notes'           => 'nullable|string|max:255',
            'payment.amount_received' => 'nullable|numeric|min:0',
            'payment.change_given'    => 'nullable|numeric|min:0',
            'priority'                => 'nullable|integer|min:0|max:2',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Session::get('user');
        if (!$user || !isset($user['id'])) {
            return back()->with('error', 'User session expired, please log in again.');
        }

        // --- Compute totals ---
        $total = collect($request->items)->sum(fn ($item) => (float) $item['price']);
        $discount = $request->input('discount_applied', 0.0);
        $netTotal = $total - $discount;
        $outstanding = $request->input('payment.amount_received')
            ? $netTotal - (float) $request->input('payment.amount_received')
            : $netTotal;

        // --- Build quotation payload ---
        $quotationData = [
            'patient_id'       => (int) $request->patient_id,
            'status'           => 'draft',
            'agreement_id'     => $request->agreement_id ? (int) $request->agreement_id : null,
            'items'            => collect($request->items)->map(fn ($item) => [
                'analysis_id' => (int) $item['analysis_id'],
                'price'       => (float) $item['price'],
            ])->values()->all(),
            'total'            => $total,
            'discount_applied' => (float) $discount,
            'net_total'        => $netTotal,
            'outstanding'      => $outstanding,
        ];

        if ($request->filled('payment.method')) {
            $quotationData['payment'] = [
                'amount'          => (float) $request->input('payment.amount'),
                'method'          => $request->input('payment.method'),
                'notes'           => $request->input('payment.notes'),
                'amount_received' => $request->input('payment.amount_received'),
                'change_given'    => $request->input('payment.change_given'),
                'outstanding'     => $outstanding,
                'user_id'         => $user['id'],
            ];
        }

        \Log::info('📤 Sending quotation payload to FastAPI', $quotationData);

        // ============================================
        // STEP 1: CREATE QUOTATION
        // ============================================
        $quotationResponse = $this->api->post('quotations', $quotationData);

        if (!$quotationResponse->successful()) {
            \Log::error('❌ Quotation creation failed', [
                'status' => $quotationResponse->status(),
                'body' => $quotationResponse->body()
            ]);
            return back()->with('error', 'Failed to create quotation.')->withInput();
        }

        $quotation = $quotationResponse->json();
        $quotationId = $quotation['id'] ?? null;

        if (!$quotationId) {
            \Log::error('❌ No quotation ID returned from API');
            return back()->with('error', 'Quotation created but ID not returned.')->withInput();
        }

        \Log::info('✅ Quotation created successfully', [
            'quotation_id' => $quotationId,
            'patient_id' => $request->patient_id
        ]);

        // ============================================
        // STEP 2: ADD PATIENT TO QUEUE
        // ============================================
        $priority = (int) $request->input('priority', 0);
        
        $queueData = [
            'patient_id'   => (int) $request->patient_id,
            'quotation_id' => $quotationId,
            'queue_type'   => 'reception',
            'priority'     => $priority,
            'notes'        => 'Added from quotation #' . $quotationId
        ];

        \Log::info('📤 Adding patient to queue', $queueData);

        try {
            $queueResponse = $this->api->post('queues', $queueData);

            if ($queueResponse->successful()) {
                $queueItem = $queueResponse->json();
                
                \Log::info('✅ Patient added to queue successfully', [
                    'queue_id' => $queueItem['id'] ?? 'unknown',
                    'position' => $queueItem['position'] ?? 'unknown',
                    'patient_id' => $request->patient_id
                ]);
                
                // Success - redirect to queue management
                return redirect()->route('queues.index')
                    ->with('success', 'Quotation created and patient added to reception queue!')
                    ->with('quotation_id', $quotationId)
                    ->with('queue_position', $queueItem['position'] ?? null)
                    ->with('show_announcement', true);
            } else {
                \Log::warning('⚠️ Failed to add patient to queue', [
                    'status' => $queueResponse->status(),
                    'body' => $queueResponse->body(),
                    'quotation_id' => $quotationId
                ]);
                
                // Quotation created but queue failed
                return redirect()->route('quotations.show', $quotationId)
                    ->with('success', 'Quotation created successfully.')
                    ->with('warning', 'Could not add patient to queue automatically. Please add manually from queue management.');
            }
        } catch (\Exception $e) {
            \Log::error('❌ Exception while adding patient to queue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'quotation_id' => $quotationId
            ]);
            
            // Quotation created but queue failed with exception
            return redirect()->route('quotations.show', $quotationId)
                ->with('success', 'Quotation created successfully.')
                ->with('warning', 'An error occurred while adding patient to queue. Please add manually.');
        }
    }

    public function storePatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'dob'               => 'required|date',
            'gender'            => 'required|string|in:H,F',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'address'           => 'nullable|string|max:255',
            'blood_type'        => 'required|string|max:3',
            'weight'            => 'nullable|numeric|min:0',
            'allergies'         => 'nullable|string',
            'medical_history'   => 'nullable|string',
            'chronic_conditions'=> 'nullable|string',
            'doctor_id'         => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Forward to FastAPI
        $payload = $validator->validated();
        $response = $this->api->post('patients', $payload);

        if (!$response->successful()) {
            return response()->json([
                'error'   => 'Failed to create patient',
                'details' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json(), 201);
    }

    public function show($id)
    {
        $response = $this->api->get("quotations/{$id}");

        if (!$response->successful()) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }

        $quotation = $response->json();
        // ✅ Outstanding now comes directly from FastAPI/DB
        return view('quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $response = $this->api->get("quotations/{$id}");
        if (!$response->successful()) {
            return redirect()->route('quotations.index')
                ->with('error', 'Quotation not found.');
        }

        $quotation  = $response->json();
        $patients   = $this->api->get('patients')->json();
        $analyses   = $this->api->get('analyses')->json();
        $agreements = $this->api->get('agreements', ['status' => 'active'])->json();

        return view('quotations.edit', compact('quotation', 'patients', 'analyses', 'agreements'));
    }

    public function update(Request $request, $id)
    {
        $payload = [
            'status'      => $request->input('status'),
            'analyses'    => $request->input('analyses', []),
            'new_payment' => $request->input('new_payment', null),
        ];

        $response = $this->api->put("quotations/{$id}", $payload);

        if ($response->successful()) {
            return redirect()->route('quotations.show', $id)
                ->with('success', 'Quotation updated successfully.');
        } else {
            return back()->with('error', 'Failed to update quotation.')->withInput();
        }
    }
    public function download($id)
{
    $response = $this->api->get("quotations/{$id}");
    if (!$response->successful()) {
        return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
    }

    $quotation = $response->json();

    // Load the PDF view — make sure this file exists at resources/views/quotations/pdf.blade.php
    $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))->setPaper('A4', 'portrait');

    $filename = 'quotation_' . $quotation['id'] . '.pdf';
    return $pdf->download($filename);
}

}
