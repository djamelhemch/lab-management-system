<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabResultController extends Controller
{
    protected $api;

    public function __construct(ApiService $apiService)
    {
        $this->api = $apiService;
    }

    /**
     * Main index - shows both chronological and patient list views
     */
public function index(Request $request)
{
     if (!$request->has('sort')) {
        return redirect()->route('lab-results.index', array_merge($request->all(), ['sort' => 'date_desc']));
    }
    $view = $request->get('view', 'chronological');
    $sort = $request->get('sort', 'date_desc'); // default newest first
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');

    try {
        // ----------------------
        // 1️⃣ Fetch lab results
        // ----------------------
        $response = $this->api->get('/lab-results');
        $data = $response->json();

        if (is_array($data)) {
            $labResults = $data;
        } elseif (isset($data['results']) && is_array($data['results'])) {
            $labResults = $data['results'];
        } else {
            $labResults = [];
        }

        // ----------------------
        // 2️⃣ Filter by date
        // ----------------------
        if ($fromDate || $toDate) {
            $labResults = array_filter($labResults, function ($item) use ($fromDate, $toDate) {
                $created = isset($item['created_at']) ? strtotime($item['created_at']) : 0;

                if ($fromDate && $created < strtotime($fromDate)) {
                    return false;
                }
                if ($toDate && $created > strtotime($toDate . ' 23:59:59')) {
                    return false;
                }
                return true;
            });
        }

        // ----------------------
        // 3️⃣ Sort results
        // ----------------------
        if ($view === 'chronological') {
            usort($labResults, function ($a, $b) use ($sort) {
                $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;

                $fileA = $a['patient']['file_number'] ?? '';
                $fileB = $b['patient']['file_number'] ?? '';

                return match($sort) {
                    'date_asc' => $dateA <=> $dateB,
                    'date_desc' => $dateB <=> $dateA,
                    'file_number_asc' => strcmp($fileA, $fileB),
                    'file_number_desc' => strcmp($fileB, $fileA),
                    default => $dateB <=> $dateA,
                };
            });
        }

        // ----------------------
        // 4️⃣ Fetch patients for patient view
        // ----------------------
        if ($view === 'patients') {
            $patientsResponse = $this->api->get('/patients');
            $patientsData = $patientsResponse->json();
            $patients = collect($patientsData);

            // Optional: sort patients by file number
            if (in_array($sort, ['file_number_asc', 'file_number_desc'])) {
                $patients = $patients->sortBy(
                    'file_number',
                    SORT_REGULAR,
                    $sort === 'file_number_desc'
                )->values();
            }
        } else {
            $patients = collect();
        }

    } catch (\Exception $e) {
        $labResults = [];
        $patients = collect();
        session()->flash('error', 'Failed to fetch data: ' . $e->getMessage());
    }
   
    // Pass filter & sort params to the view for Blade dropdowns & inputs
    return view('lab_results.index', compact('labResults', 'patients', 'view', 'sort', 'fromDate', 'toDate'));
}
    /**
     * Show single lab result details
     */
    public function show($id)
    {
        try {
            Log::info("📡 Fetching lab result with ID: {$id}");
            
            $response = $this->api->get("/lab-results/{$id}");
            $result = $response->json();
            
            Log::info('✅ Single lab result fetched', ['id' => $id]);
            
        } catch (\Exception $e) {
            Log::error("❌ Failed to fetch lab result ID {$id}: " . $e->getMessage());
            abort(404, 'Result not found or API error.');
        }
        
        return view('lab_results.show', compact('result'));
    }

    /**
     * Get patient-specific results (chronological matrix view)
     */
    public function patientResults($patientId)
    {
        try {
            Log::info("📡 Fetching results for patient ID: {$patientId}");
            
            // API Call: Get patient results grouped by category
            $response = $this->api->get("/patients/{$patientId}/results");
            $data = $response->json();
            
            Log::info('✅ Patient results fetched', [
                'patient_id' => $patientId,
                'patient_name' => ($data['patient']['first_name'] ?? '') . ' ' . ($data['patient']['last_name'] ?? ''),
                'categories_count' => count($data['categories'] ?? [])
            ]);
            
            // Check if data is valid
            if (!isset($data['patient']) || !isset($data['categories'])) {
                Log::error('❌ Invalid patient results structure', ['data' => $data]);
                abort(404, 'Invalid patient data received from API');
            }
            
            return view('lab_results.partials.patient_detail', compact('data'));
            
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("❌ API request failed for patient {$patientId}:", [
                'status' => $e->response->status() ?? 'unknown',
                'message' => $e->getMessage()
            ]);
            
            if (method_exists($e, 'response') && $e->response && $e->response->status() === 404) {
                abort(404, 'Patient not found');
            }
            
            abort(500, 'Failed to load patient results');
            
        } catch (\Exception $e) {
            Log::error("❌ Failed to fetch patient results: " . $e->getMessage());
            abort(500, 'Failed to load patient results');
        }
    }

    /**
     * Store new lab result
     */
    public function create(Request $request)
    {
        // Page 1, bigger limit so dropdown is usable; adapt as needed
        $response = $this->api->get('quotations', [
            'query' => [
                'page'  => 1,
                'limit' => 50,
            ],
        ]);

        $quotations = [];
        if ($response->successful()) {
            $body = $response->json();   // has 'items', 'total', ...
            $quotations = $body['items'] ?? [];
        }

        return view('lab_results.create', [
            'quotations' => $quotations,
            'fastapi_token'   => session('token')
        ]);
    }
    public function storeBulk(Request $request)
{
    $validated = $request->validate([
        'quotation_id'        => 'required|integer',
        'result_values'       => 'required|array',
        'result_values.*'     => 'nullable|string',
    ]);

    $payload = [
        'quotation_id'  => (int) $validated['quotation_id'],
        'result_values' => $validated['result_values'], // item_id => value
    ];

    $response = $this->api->post('lab-results/bulk', $payload, ['json' => true]);

    if ($response->successful()) {
        return redirect()
            ->route('lab-results.index')
            ->with('success', '✅ Résultat ajouté avec succès !');
    }

    return back()
        ->with('error', 'Échec de la création des résultats.')
        ->withInput();
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'quotation_item_id' => 'required|integer',
            'result_value'      => 'required|string',
        ]);

        // Call FastAPI POST /lab-results
        $response = $this->api->post('lab-results', $data, ['json' => true]); // same pattern as your analyses controller [web:31][web:39]

        if ($response->successful()) {
            return redirect()
                ->route('lab-results.index')
                ->with('success', '✅ Résultat ajouté avec succès !');
        }

        return back()
            ->with('error', 'Échec de la création du résultat.')
            ->withInput();
    }

    /**
     * Download lab result as PDF
     */
    public function download($id)
    {
        try {
            Log::info("📡 Downloading lab result PDF for ID: {$id}");
            
            // Assuming your FastAPI has a PDF endpoint
            $response = $this->api->get("/lab-results/{$id}/pdf");
            
            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=result-{$id}.pdf");
                
        } catch (\Exception $e) {
            Log::error("❌ Failed to download result {$id}: " . $e->getMessage());
            abort(404, 'Failed to generate PDF');
        }
    }
}
