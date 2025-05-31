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

}
