<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\AnalysisController;

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
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.analysis_id' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Build the correct payload
        $data = [
            'patient_id' => $request->patient_id,
            'status' => 'draft',
            'agreement_id' => $request->agreement_id,
            'items' => array_values($request->items), 
        ];

        $response = $this->api->post('quotations', $data);

        return $response->successful()
            ? redirect()->route('quotations.index')->with('success', 'Quotation created.')
            : back()->with('error', 'Failed to create quotation.')->withInput();
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
}
