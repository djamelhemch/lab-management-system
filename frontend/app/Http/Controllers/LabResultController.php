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
        $view = $request->get('view', 'chronological');
        
        try {
            // API Call #1: Get all lab results (for chronological view)
            Log::info('📡 Fetching lab results from FastAPI...');
            $response = $this->api->get('/lab-results');
            $data = $response->json();
            
            if (is_array($data)) {
                $labResults = $data;
            } elseif (isset($data['results']) && is_array($data['results'])) {
                $labResults = $data['results'];
            } else {
                Log::warning('⚠️ Unexpected response structure', ['response' => $data]);
                $labResults = [];
            }
            
            Log::info('✅ Received lab results', ['count' => count($labResults)]);
            
            // API Call #2: Get all patients (for patient list view)
            if ($view === 'patients') {
                Log::info('📡 Fetching patients list from FastAPI...');
                $patientsResponse = $this->api->get('/patients');
                $patientsData = $patientsResponse->json();
                
                Log::info('✅ Received patients', [
                    'count' => count($patientsData),
                    'sample' => count($patientsData) > 0 ? $patientsData[0] : null
                ]);
                
                $patients = collect($patientsData);
            } else {
                $patients = collect();
            }
            
        } catch (\Exception $e) {
            $labResults = [];
            $patients = collect();
            $errorMessage = 'Failed to fetch data: ' . $e->getMessage();
            
            Log::error('❌ API Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            session()->flash('error', $errorMessage);
        }
        
        return view('lab_results.index', compact('labResults', 'patients', 'view'));
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
    public function store(Request $request)
    {
        try {
            Log::info('📡 Creating new lab result');
            
            $response = $this->api->post('/lab-results', $request->all());
            $result = $response->json();
            
            Log::info('✅ Lab result created', ['id' => $result['id'] ?? null]);
            
            return redirect()
                ->route('lab-results.show', $result['id'])
                ->with('success', '[translate:Résultat créé avec succès]');
                
        } catch (\Exception $e) {
            Log::error('❌ Failed to create lab result: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', '[translate:Échec de création du résultat]');
        }
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
