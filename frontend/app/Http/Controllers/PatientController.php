<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class PatientController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('patients');
        $patients = $response->successful() ? $response->json() : [];
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $this->api->post('patients', $request->except('_token'));
        return redirect()->route('patients.index');
    }

    public function show($id)
    {
        $response = $this->api->get("patients/{$id}");
        $patient = $response->successful() ? $response->json() : null;
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $response = $this->api->get("patients/{$id}");
        $patient = $response->successful() ? $response->json() : null;
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $this->api->put("patients/{$id}", $request->except('_token'));
        return redirect()->route('patients.index');
    }

    public function destroy($id)
    {
        $this->api->delete("patients/{$id}");
        return redirect()->route('patients.index');
    }
}