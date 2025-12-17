<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;  
use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

    public function update(Request $request)
    {
        $request->validate([
            'marquee_text' => 'required|string|max:500',
            'setting_id' => 'required|integer',
            'option_id' => 'required|integer'
        ]);
        
        try {
            $response = $this->api->put("/settings/{$request->setting_id}/options/{$request->option_id}", [
                'value' => $request->marquee_text
            ]);
            
            if ($response->successful()) {
                return back()->with('success', 'Banner text updated successfully');
            }
            
            $errorData = $response->json();
            return back()->with('error', $errorData['detail'] ?? 'Failed to update settings');
            
        } catch (\Exception $e) {
            Log::error('Settings update error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Service unavailable');
        }
    }
}
