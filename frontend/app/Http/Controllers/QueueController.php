<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QueueController extends Controller
{
    protected $apiBase;

    public function __construct()
    {
        $this->apiBase = env('FASTAPI_URL', 'http://localhost:8000'); // your FastAPI base URL
    }

    // Show both queues
    public function index()
    {
        // Fetch queues
        $responseQueues = Http::get("{$this->apiBase}/queues");
        if ($responseQueues->failed()) {
            return back()->withErrors('Failed to load queues.');
        }
        $queues = $responseQueues->json();

        // Fetch patients
        $responsePatients = Http::get("{$this->apiBase}/patients");
        if ($responsePatients->failed()) {
            return back()->withErrors('Failed to load patients.');
        }
        $patientsData = $responsePatients->json();

        // Create id => full_name map for patients
        $patients = [];
        foreach ($patientsData as $patient) {
            $patients[$patient['id']] = $patient['full_name'] ?? ($patient['first_name'] . ' ' . $patient['last_name']);
        }

        return view('queues.index', [
            'receptionQueue' => $queues['reception'] ?? [],
            'bloodDrawQueue' => $queues['blood_draw'] ?? [],
            'patients' => $patients,
        ]);
    }

    // Add patient to queue
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'quotation_id' => 'nullable|integer',
            'type' => 'required|in:reception,blood_draw',
        ]);

        $response = Http::post("{$this->apiBase}/queues", $data);

        if ($response->successful()) {
            return redirect()->route('queues.index')->with('success', 'Patient added to queue.');
        }

        return back()->withErrors('Failed to add patient to queue.')->withInput();
    }

    // Remove patient from queue
    public function destroy($id)
    {
        $response = Http::delete("{$this->apiBase}/queues/{$id}");

        if ($response->successful()) {
            return redirect()->route('queues.index')->with('success', 'Patient removed from queue.');
        }

        return back()->withErrors('Failed to remove patient from queue.');
    }

    // Move next patient from reception to blood_draw queue
    public function moveNext()
    {
        $response = Http::post("{$this->apiBase}/queues/move-next");

        if ($response->successful()) {
            return redirect()->route('queues.index')->with('success', 'Moved next patient to blood draw queue.');
        }

        return back()->withErrors('Failed to move patient.');
    }
}
