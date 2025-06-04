<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('doctors');
        $doctors = $response->successful() ? $response->json() : [];

        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        // Manually coerce checkbox value to a boolean
        $request->merge([
            'is_prescriber' => $request->has('is_prescriber') ? true : false
        ]);

        // Now validation will pass correctly
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:100',
            'specialty' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'is_prescriber' => 'boolean', // this will now work
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['full_name', 'specialty', 'phone', 'email', 'address', 'is_prescriber']);

        $response = $this->api->post('doctors', $data);

        if ($response->successful()) {
            return redirect()->route('doctors.index')->with('success', 'Doctor created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create doctor.')->withInput();
        }
    }

    public function update(Request $request, $id)  
    {  
        // Manually coerce checkbox value to a boolean  
        $request->merge([  
            'is_prescriber' => $request->has('is_prescriber') ? true : false  
        ]);  
    
        $validator = Validator::make($request->all(), [  
            'full_name' => 'required|string|max:100',  
            'specialty' => 'nullable|string|max:100',  
            'phone' => 'nullable|string|max:20',  
            'email' => 'nullable|email|max:100',  
            'address' => 'nullable|string',  
            'is_prescriber' => 'boolean',  
        ]);  
    
        if ($validator->fails()) {  
            return redirect()  
                ->back()  
                ->withErrors($validator)  
                ->withInput();  
        }  
    
        $data = $request->only(['full_name', 'specialty', 'phone', 'email', 'address', 'is_prescriber']);  
    
        $response = $this->api->put("doctors/{$id}", $data);  
    
        if ($response->successful()) {  
            return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');  
        } else {  
            return redirect()->back()->with('error', 'Failed toupdate doctor.')->withInput();  
        }  
    }

    public function edit($id)  
    {  
        $doctorResponse = $this->api->get("doctors/{$id}");  
    
        if (!$doctorResponse->successful()) {  
            return redirect()->route('doctors.index')->with('error', 'Doctor not found.');  
        }  
    
        $doctor = $doctorResponse->json();  
    
        return view('doctors.edit', compact('doctor'));  
    }

    public function show($id)  
    {  
        // Get doctor details  
        $doctorResponse = $this->api->get("doctors/{$id}");  
          
        if (!$doctorResponse->successful()) {  
            return redirect()->route('doctors.index')->with('error', 'Doctor not found.');  
        }  
          
        $doctor = $doctorResponse->json();  
          
        // Get patients for this doctor  
        $patientsResponse = $this->api->get("doctors/{$id}/patients");  
        $patients = $patientsResponse->successful() ? $patientsResponse->json() : [];  
          
        return view('doctors.show', compact('doctor', 'patients'));  
    }

    public function patientsTable(Request $request, $doctorId)
    {
        $params = [];
        if ($request->filled('q')) {
            $params['q'] = $request->input('q');
        }

        $patientsResponse = $this->api->get("doctors/{$doctorId}/patients/table", $params);

        if (!$patientsResponse->successful()) {
            return response('Error fetching patients', 500);
        }

        $patients = $patientsResponse->json();

        return view('doctors.partials.patients_table', compact('patients'));
    }
}