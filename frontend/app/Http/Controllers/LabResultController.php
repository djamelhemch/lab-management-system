<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class LabResultController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    // 🔹 List all lab results
    public function index()
    {
        try {
            Log::info('📡 Fetching lab results from FastAPI...');

            $response = $this->api->get('/lab-results');

            // Decode safely
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
        } catch (\Exception $e) {
            $labResults = [];
            $errorMessage = 'Failed to fetch lab results: ' . $e->getMessage();
            Log::error('❌ ' . $errorMessage);
            session()->flash('error', $errorMessage);
        }

        return view('lab_results.index', compact('labResults'));
    }

    // 🔹 Show details of a single result
    public function show($id)
    {
        try {
            Log::info("📡 Fetching lab result with ID: {$id}");

            $response = $this->api->get("/lab-results/{$id}");
            $result = $response->json(); // ✅ decode to array

            Log::info('✅ Single lab result fetched', ['id' => $id]);

        } catch (\Exception $e) {
            Log::error("❌ Failed to fetch lab result ID {$id}: " . $e->getMessage());
            abort(404, 'Result not found or API error.');
        }

        return view('lab_results.show', compact('result'));
    }


    // 🔹 Create (POST) a new result
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_item_id' => 'required|numeric',
            'result_value' => 'required',
        ]);

        try {
            $payload = [
                'quotation_item_id' => $validated['quotation_item_id'],
                'result_value' => $validated['result_value'],
            ];

            Log::info('🧾 Sending new lab result payload to FastAPI:', ['payload' => $payload]);

            $result = $this->api->post('/lab-results', $payload);
            Log::info('✅ API returned new lab result:', ['result' => $result]);

            return redirect()->route('lab-results.index')->with('success', 'Result added successfully!');
        } catch (\Exception $e) {
            Log::error('❌ Failed to create lab result: ' . $e->getMessage());
            return back()->with('error', 'Failed to create lab result: ' . $e->getMessage());
        }
    }
    public function download($id)
    {
        try {
            $result = $this->api->get("/lab-results/{$id}")->json();

            if (!$result) {
                abort(404, 'Result not found.');
            }

            $pdf = \PDF::loadView('lab_results.pdf', compact('result'))
                ->setPaper('a4', 'portrait');

            $filename = 'Resultat_' . ($result['file_number'] ?? 'Unknown') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('❌ PDF generation failed: ' . $e->getMessage());
            abort(500, 'Unable to generate PDF report.');
        }
    }

}
