<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;

class AgreementController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $agreements = $this->api->get('agreements')->json();
        return view('agreements.index', compact('agreements'));
    }

    public function create()
    {
        $patients = $this->api->get('patients')->json();
        $doctors = $this->api->get('doctors')->json();
        return view('agreements.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $response = $this->api->post('agreements', $request->all());

        return $response->successful()
            ? redirect()->route('agreements.index')->with('success', 'Agreement created.')
            : back()->with('error', 'Failed to create agreement.')->withInput();
    }

    public function show($id)
    {
        $agreement = $this->api->get("agreements/{$id}")->json();
        return view('agreements.show', compact('agreement'));
    }

    public function edit($id)
    {
        $agreement = $this->api->get("agreements/{$id}")->json();
        $patients = $this->api->get('patients')->json();
        $doctors = $this->api->get('doctors')->json();

        return view('agreements.edit', compact('agreement', 'patients', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $response = $this->api->put("agreements/{$id}", $request->all());

        return $response->successful()
            ? redirect()->route('agreements.index')->with('success', 'Agreement updated.')
            : back()->with('error', 'Failed to update agreement.')->withInput();
    }

    public function destroy($id)
    {
        $response = $this->api->delete("agreements/{$id}");

        return $response->successful()
            ? redirect()->route('agreements.index')->with('success', 'Agreement deleted.')
            : back()->with('error', 'Failed to delete agreement.');
    }
}
