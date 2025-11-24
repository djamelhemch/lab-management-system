<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiService;

class SettingsController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/settings');

        if (!$response->ok()) {
            return back()->with('error', 'Failed to fetch settings.');
        }

        $settings = $response->json();
        return view('admin.settings.index', compact('settings'));
    }

    public function addOption(Request $request, $id)
    {
        $data = $request->validate([
            'value' => 'required|string',
            'is_default' => 'nullable|boolean'
        ]);

        $response = $this->api->post("/settings/{$id}/options", $data);

        if (!$response->ok()) {
            return back()->with('error', 'Failed to add option.');
        }

        return back()->with('success', 'Option added successfully.');
    }

    public function deleteOption($id)
    {
        $response = $this->api->delete("/settings/options/{$id}");

        if (!$response->ok()) {
            return back()->with('error', 'Failed to delete option.');
        }

        return back()->with('success', 'Option deleted successfully.');
    }
    public function setDefault($id, $optionId)
    {
        $response = $this->api->put("/settings/{$id}/options/{$optionId}/default", []);

        if (!$response->ok()) {
            return back()->with('error', 'Failed to set default option.');
        }

        // Clear cache so app updates immediately
        app(\App\Services\SettingService::class)->clearCache('currency');

        return back()->with('success', 'Default option updated.');
    }
}
