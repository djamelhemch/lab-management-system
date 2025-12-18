<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    private $apiUrl;
    private $timeout = 15;

    public function __construct()
    {
        $this->apiUrl = env('FASTAPI_URL', 'http://localhost:8000');
    }

    /**
     * Display queue management dashboard
     */
    public function index()
    {
        try {
            // Fetch queues
            $queuesResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/queues");

            if ($queuesResponse->failed()) {
                throw new \Exception('Failed to fetch queues');
            }

            $queues = $queuesResponse->json();

            // Fetch patients for dropdown
            $patientsResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/patients");

            $patients = $patientsResponse->successful() 
                ? $patientsResponse->json() 
                : [];

            // Format patients for select dropdown
            $patientOptions = [];
            foreach ($patients as $patient) {
                $name = trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
                $patientOptions[$patient['id']] = $name ?: "Patient #{$patient['id']}";
            }

            return view('queues.index', [
                'receptionQueue' => $queues['reception'] ?? [],
                'bloodDrawQueue' => $queues['blood_draw'] ?? [],
                'patients' => $patientOptions
            ]);

        } catch (\Exception $e) {
            Log::error('Queue index error', ['error' => $e->getMessage()]);
            
            return view('queues.index', [
                'receptionQueue' => [],
                'bloodDrawQueue' => [],
                'patients' => []
            ])->withErrors('Unable to load queue data. Please try again.');
        }
    }

    /**
     * Add patient to queue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'queue_type' => 'required|in:reception,blood_draw',
            'priority' => 'nullable|integer|min:0|max:2',
            'quotation_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/queues", $validated);

            if ($response->successful()) {
                return redirect()
                    ->route('queues.index')
                    ->with('success', 'Patient added to queue successfully');
            }

            $error = $response->json()['detail'] ?? 'Failed to add patient to queue';
            return back()->withErrors($error)->withInput();

        } catch (\Exception $e) {
            Log::error('Add to queue error', ['error' => $e->getMessage()]);
            return back()
                ->withErrors('An error occurred. Please try again.')
                ->withInput();
        }
    }

    /**
     * Move next patient from reception to blood draw
     */
    public function moveNext(Request $request)
    {
        try {
            Log::info('🔄 Move next called');
            
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/queues/move-next");

            Log::info('📥 Move next response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('✅ Move next successful', ['data' => $data]);
                
                // Return JSON for AJAX requests
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Patient moved successfully',
                        'id' => $data['id'] ?? null,
                        'patient_id' => $data['patient_id'] ?? null,
                        'patient_name' => $data['patient_name'] ?? 'Patient',
                        'position' => $data['position'] ?? 1,
                        'queue_type' => $data['queue_type'] ?? 'blood_draw'
                    ]);
                }
                
                // Traditional redirect for non-AJAX
                session()->flash('next_patient', [
                    'id' => $data['id'],
                    'patient_id' => $data['patient_id'],
                    'patient_name' => $data['patient_name'] ?? 'Patient',
                    'position' => $data['position']
                ]);
                
                return redirect()
                    ->route('queues.index')
                    ->with('success', 'Patient moved to blood draw successfully');
            }

            $error = $response->json()['detail'] ?? 'Failed to move patient';
            
            Log::error('❌ Move next failed', [
                'error' => $error,
                'response' => $response->body()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $error
                ], 400);
            }
            
            return back()->withErrors($error);

        } catch (\Exception $e) {
            Log::error('❌ Move next exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred'
                ], 500);
            }
            
            return back()->withErrors('An error occurred. Please try again.');
        }
    }


    /**
     * Update queue item priority
     */
    public function updatePriority(Request $request, $id)
{
    $validated = $request->validate([
        'priority' => 'required|integer|min:0|max:2'
    ]);

    try {
        // Fix: Use query parameter properly
        $response = Http::timeout($this->timeout)
            ->put("{$this->apiUrl}/queues/{$id}/priority?priority={$validated['priority']}");

        if ($response->successful()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Priority updated successfully'
                ]);
            }

            return redirect()
                ->route('queues.index')
                ->with('success', 'Priority updated successfully');
        }

        $error = $response->json()['detail'] ?? 'Failed to update priority';
        
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $error], 400);
        }

        return back()->withErrors($error);

    } catch (\Exception $e) {
        Log::error('Update priority error', ['error' => $e->getMessage()]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred'
            ], 500);
        }

        return back()->withErrors('An error occurred. Please try again.');
    }
}

    /**
     * Remove patient from queue
     */
    public function destroy(Request $request, $id)
    {
        try {
            $reason = $request->input('reason', 'Manual removal');
            
            $response = Http::timeout($this->timeout)
                ->delete("{$this->apiUrl}/queues/{$id}", [
                    'reason' => $reason
                ]);

            if ($response->successful() || $response->status() === 204) {
                return redirect()
                    ->route('queues.index')
                    ->with('success', 'Patient removed from queue');
            }

            return back()->withErrors('Failed to remove patient from queue');

        } catch (\Exception $e) {
            Log::error('Remove from queue error', ['error' => $e->getMessage()]);
            return back()->withErrors('An error occurred. Please try again.');
        }
    }

    /**
     * Display waiting room screen
     */
    public function show()
    {
        try {
            $queuesResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/queues");

            $statusResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/queues/status");
            
            // Get marquee banner default option
            $marqueeResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/settings/default/marquee_banner");
            $videoResponse = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/settings/default/queue_video");
            $queues = $queuesResponse->successful() ? $queuesResponse->json() : ['reception' => [], 'blood_draw' => []];
            $status = $statusResponse->successful() ? $statusResponse->json() : null;
            $videoSrc = $videoResponse->successful()
                ? $videoResponse->json()['value']
                : '/videos/lab_video.mp4';
            $marqueeText = $marqueeResponse->successful() 
                ? $marqueeResponse->json()['value'] 
                : "L'ÉTABLISSEMENT \"ABDELATIF LAB\" LABORATOIRE D''ANALYSES DE SANG CONVENTIONNÉ AVEC LE LABORATOIRE CERBA EN FRANCE VOUS SOUHAITE LA BIENVENUE, LE LABORATOIRE EST OUVERT DU SAMEDI AU JEUDI DE 7H30 à 16H30.";

            return view('queues.show', [
                'bloodDrawQueue' => $queues['blood_draw'] ?? [],
                'status' => $status,
                'marqueeText' => $marqueeText,
                'videoSrc' => $videoSrc,
            ]);

        } catch (\Exception $e) {
            Log::error('Waiting room display error', ['error' => $e->getMessage()]);
            
            return view('queues.show', [
                'bloodDrawQueue' => [],
                'status' => null,
                'marqueeText' => "L'ÉTABLISSEMENT \"ABDELATIF LAB\" LABORATOIRE D''ANALYSES DE SANG CONVENTIONNÉ AVEC LE LABORATOIRE CERBA EN FRANCE VOUS SOUHAITE LA BIENVENUE, LE LABORATOIRE EST OUVERT DU SAMEDI AU JEUDI DE 7H30 à 16H30."
            ]);
        }
    }

    /**
     * Get queue status (AJAX endpoint)
     */
    public function getQueueStatus()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/queues/status");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Failed to fetch status'], 500);

        } catch (\Exception $e) {
            Log::error('Get status error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
