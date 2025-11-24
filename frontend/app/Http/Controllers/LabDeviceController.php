<?php

// app/Http/Controllers/LabDeviceController.php
namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class LabDeviceController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        try {
            \Log::info('Fetching lab devices from API');
            
            $resp = $this->api->get('/lab-devices');
            
            \Log::info('API Response', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            
            $devices = $resp->json() ?? [];
            
            if ($request->ajax()) {
                return view('lab_devices.partials.device_table_rows', compact('devices'))->render();
            }

            return view('lab_devices.index', compact('devices'));
            
        } catch (\Exception $e) {
            \Log::error('Lab devices error', ['error' => $e->getMessage()]);
            
            // Return empty view instead of error
            $devices = [];
            return view('lab_devices.index', compact('devices'));
        }
    }
    public function create()
    {
        return view('lab_devices.create');
    }

    public function store(Request $request)
    {
        $resp = $this->api->post('/lab-devices', $request->all());
        if ($resp->ok()) {
            return redirect()->route('lab-devices.index')->with('success', 'Device created!');
        } else {
            return back()->withInput()->with('error', $resp->body());
        }
    }

    public function edit($id)
    {
        $resp = $this->api->get("/lab-devices/$id");
        $device = $resp->json();
        return view('lab_devices.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $resp = $this->api->put("/lab-devices/$id", $request->all());
        if ($resp->ok()) {
            return redirect()->route('lab-devices.index')->with('success', 'Device updated!');
        } else {
            return back()->withInput()->with('error', $resp->body());
        }
    }

    public function destroy($id)
    {
        $this->api->delete("/lab-devices/$id");
        return redirect()->route('lab-devices.index')->with('success', 'Device deleted!');
    }
}
