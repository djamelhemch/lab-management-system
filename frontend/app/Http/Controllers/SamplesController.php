<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SamplesController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('FASTAPI_URL', 'http://localhost:8000');
    }

    // 🧪 List all samples
    public function index(Request $request)
    {
        $search = $request->input('q');
        $status = $request->input('status');

        try {
            $apiUrl = $this->apiBaseUrl . '/samples';
            $queryParams = [];

            if ($search) $queryParams['q'] = $search;
            if ($status) $queryParams['status'] = $status;

            if (!empty($queryParams)) {
                $apiUrl .= '?' . http_build_query($queryParams);
            }

            $response = Http::get($apiUrl);
            $samples = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error('Samples API error: ' . $e->getMessage());
            $samples = [];
        }

        return view('samples.index', compact('samples'));
    }

        // 🧬 Show create form
    public function create()
    {
        $doctors = $this->getDoctors();
        $patients = $this->getPatients();

        $devicesResponse = Http::get($this->apiBaseUrl . '/lab-devices');
        $analysesResponse = Http::get($this->apiBaseUrl . '/analyses');

        $devices = $devicesResponse->successful() ? $devicesResponse->json() : [];
        $analyses = $analysesResponse->successful() ? $analysesResponse->json() : [];

        // Ensure $devices is an array
        if (!is_array($devices)) {
            $devices = [];
        }

        return view('samples.create', compact('doctors', 'patients', 'devices', 'analyses'));
    }

    // 🧠 Edit form
    public function edit($id)
    {
        try {
            $response = Http::get($this->apiBaseUrl . "/samples/{$id}");
            $sample = $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Sample edit API error: ' . $e->getMessage());
            $sample = null;
        }

        if (!$sample) {
            return redirect()->route('samples.index')->withErrors(['error' => 'Sample not found.']);
        }

        $doctors = $this->getDoctors();
        $patients = $this->getPatients();
        $devices = $this->getLabDevices();

        return view('samples.edit', compact('sample', 'doctors', 'patients', 'devices'));
    }

    // 🔍 AJAX Patient Search
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');
        try {
            $response = Http::get($this->apiBaseUrl . '/patients', ['q' => $query, 'limit' => 20]);
            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            Log::error('Patient search API error: ' . $e->getMessage());
        }
        return response()->json([]);
    }

    // 🔍 AJAX Doctor Search
    public function searchDoctors(Request $request)
    {
        $query = $request->get('q', '');
        try {
            $response = Http::get($this->apiBaseUrl . '/doctors', ['q' => $query, 'limit' => 20]);
            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            Log::error('Doctor search API error: ' . $e->getMessage());
        }
        return response()->json([]);
    }

    // ⚙️ Store new sample
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            $response = Http::post($this->apiBaseUrl . '/samples', $data);
            if ($response->successful()) {
                return redirect()->route('samples.index')->with('success', 'Sample created successfully.');
            } else {
                return back()->withErrors(['error' => 'Failed to create sample.'])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Sample create API error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create sample.'])->withInput();
        }
    }

    // ⚙️ Update sample
    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $response = Http::put($this->apiBaseUrl . "/samples/{$id}", $data);
            if ($response->successful()) {
                return redirect()->route('samples.show', $id)->with('success', 'Sample updated successfully.');
            } else {
                return back()->withErrors(['error' => 'Failed to update sample.'])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Sample update API error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update sample.'])->withInput();
        }
    }

    // 👁️ View single sample
    public function show($id)
    {
        try {
            $response = Http::get($this->apiBaseUrl . "/samples/{$id}");
            $sample = $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Sample show API error: ' . $e->getMessage());
            $sample = null;
        }

        if (!$sample) {
            return redirect()->route('samples.index')->withErrors(['error' => 'Sample not found.']);
        }

        return view('samples.show', compact('sample'));
    }

    // 🔄 Update sample status
    public function updateStatus(Request $request, $id)
    {
        $data = $request->only(['status', 'rejection_reason']);
        try {
            $response = Http::put($this->apiBaseUrl . "/samples/{$id}/status", $data);
            if ($response->successful()) {
                return redirect()->route('samples.show', $id)->with('success', 'Sample status updated.');
            } else {
                return back()->withErrors(['error' => 'Failed to update status.']);
            }
        } catch (\Exception $e) {
            Log::error('Sample status update API error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update status.']);
        }
    }

    // 🧠 Helpers
    private function getDoctors()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/doctors?limit=100');
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error('Doctors API error: ' . $e->getMessage());
            return [];
        }
    }

    private function getPatients()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/patients?limit=100');
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error('Patients API error: ' . $e->getMessage());
            return [];
        }
    }

    private function getLabDevices()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/devices?limit=100');
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error('Lab devices API error: ' . $e->getMessage());
            return [];
        }
    }
}
