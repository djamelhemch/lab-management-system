<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)  
    {  
        Log::info('Index method called', [  
            'all_request_data' => $request->all(),  
            'q' => $request->input('q'),  
            'q_filled' => $request->filled('q')  
        ]);  
  
        $params = [];  
        if ($request->filled('q')) {  
            $params['q'] = $request->input('q');  
        }  
  
        Log::info('Fetching patients from API', [  
            'endpoint' => 'patients',  
            'params' => $params  
        ]);  
  
        $patientsResponse = $this->api->get('patients', $params);  
  
        Log::info('API Response received', [  
            'status' => $patientsResponse->status(),  
            'successful' => $patientsResponse->successful(),  
            'headers' => $patientsResponse->headers(),  
        ]);  
  
        if (!$patientsResponse->successful()) {  
            Log::error('API patients fetch failed', [  
                'status' => $patientsResponse->status(),  
                'body' => $patientsResponse->body(),  
                'json' => $patientsResponse->json()  
            ]);  
        }  
  
        $patients = $patientsResponse->successful() ? $patientsResponse->json() : [];  
  
        Log::info('Returning view', [  
            'patients_count' => count($patients)  
        ]);  
  
        return view('patients.index', compact('patients'));  
    }  
  
    public function table(Request $request)
    {
        try {
            $params = [];
            if ($request->filled('q')) {
                $params['q'] = $request->input('q');
            }

            $patientsResponse = $this->api->get('patients/table', $params);

            if (!$patientsResponse->successful()) {
                \Log::error('API patients fetch (table) failed', [
                    'status' => $patientsResponse->status(),
                    'body' => $patientsResponse->body(),
                    'json' => $patientsResponse->json()
                ]);
                return response('Error fetching patients', 500);
            }

            $patients = $patientsResponse->json();

            // Make sure $patients is an array
            if (!is_array($patients)) {
                \Log::error('API returned non-array for patients', ['patients' => $patients]);
                $patients = [];
            }

            return view('patients.partials.table', compact('patients'));
        } catch (\Throwable $e) {
            \Log::error('Exception in PatientsController@table', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('Internal Server Error', 500);
        }
    }
    

    public function create()  
    {  
        $doctors = $this->api->get('doctors')->json() ?? [];  
        return view('patients.create', compact('doctors'));  
    }

    public function store(Request $request)  
    {  
        $data = $request->except('_token');  

        // Ensure doctor_id is sent as an integer or null
       $data = $request->except('_token');  
        if (isset($data['doctor_id'])) {  
            $data['doctor_id'] = (int) $data['doctor_id'];  
        } else {  
            $data['doctor_id'] = null;  
        }
        // Send to API
        $response = $this->api->post('patients', $data);

        // Logging
        Log::info('API Response Status: ' . $response->status());  
        Log::info('API Response Body: ' . $response->body());  
        Log::info('Request data sent to API:', $data);

        if ($response->failed()) {  
            Log::error('Failed to create patient', [  
                'status' => $response->status(),  
                'body' => $response->body(),  
            ]);  
            return back()->withErrors(['error' => 'Failed to create patient']);  
        }  

        return redirect()->route('patients.index');  
    }

    public function show($id)
    {
        $response = $this->api->get("patients/{$id}");
        $patient = $response->successful() ? $response->json() : null;

        $doctorFullName = null;
        if ($patient && isset($patient['doctor_id'])) {
            $doctorResponse = $this->api->get("doctors/{$patient['doctor_id']}");
            if ($doctorResponse->successful()) {
                $doctor = $doctorResponse->json();
                $doctorFullName = $doctor['full_name'] ?? null;
            }
        }

        // Inject into the patient array for convenience
        if ($patient) {
            $patient['doctor_full_name'] = $doctorFullName;
        }

        return view('patients.show', compact('patient'));
    }
    
    public function edit($id)  
    {  
        $response = $this->api->get("patients/{$id}");  
        $patient = $response->successful() ? $response->json() : null;  
        $doctors = $this->api->get('doctors')->json() ?? [];  
        return view('patients.edit', compact('patient', 'doctors'));  
    }

    public function update(Request $request, $id)  
    {  
        $data = $request->except('_token', '_method');  
    
        // Decode URL-encoded values  
        $data = Arr::map($data, function ($value) {  
            return is_string($value) ? urldecode($value) : $value;  
        });  
    
        // Convert empty strings to null  
        $data = Arr::map($data, function ($value) {  
            return $value === '' ? null : $value;  
        });  
    
        // Ensure weight is a float or null  
        if (isset($data['weight'])) {  
            $data['weight'] = is_numeric($data['weight']) ? (float)$data['weight'] : null;  
        }  
    
        $this->api->put("patients/{$id}", $data);  
        return redirect()->route('patients.index');  
    }

    public function destroy($id)
    {
        $this->api->delete("patients/{$id}");
        return redirect()->route('patients.index');
    }
}