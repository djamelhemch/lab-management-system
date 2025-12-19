<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;  
use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Services\SettingService;
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

    // Grouping logic
    $grouped = [
        'general' => [],
        'queue' => [],
        'other' => [],
    ];

    foreach ($settings as $setting) {
        switch ($setting['name']) {
            case 'marquee_banner':
            case 'queue_video':
                $grouped['queue'][] = $setting;
                break;

            case 'currency':
            case 'logo':
                $grouped['general'][] = $setting;
                break;

            default:
                $grouped['other'][] = $setting;
                break;
        }
    }

    // Labels for groups
    $groupLabels = [
        'queue'   => 'Queue settings',
        'general' => 'General settings',
        'other'   => 'Other settings',
    ];

    return view('admin.settings.index', [
        'groupedSettings' => $grouped,
        'groupLabels'     => $groupLabels,
    ]);
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
    public function updateVideo(Request $request)
{
    $request->validate([
        'setting_id' => 'required|integer',
        'option_id'  => 'required|integer',
        'video'      => 'required|file|mimetypes:video/mp4|max:51200', // 50MB
    ]);

    try {
        $file = $request->file('video');

        // 1. Delete any existing queue videos
        //    (all mp4 files in videos/queue/ for example)
        $disk = \Storage::disk('public'); // storage/app/public
        $directory = 'videos/queue';
        $existingFiles = $disk->files($directory);
        foreach ($existingFiles as $existing) {
            // delete only .mp4 files
            if (strtolower(pathinfo($existing, PATHINFO_EXTENSION)) === 'mp4') {
                $disk->delete($existing);
            }
        }

        // 2. Store new video with its original name
        $originalName = $file->getClientOriginalName(); // e.g. my_video.mp4
        $path = $file->storeAs($directory, $originalName, 'public'); // videos/queue/my_video.mp4

        // 3. Build public URL
        // make sure you ran: php artisan storage:link
        $publicUrl = asset('storage/' . $path);

        // 4. Update FastAPI setting option value
        $response = $this->api->put("/settings/{$request->setting_id}/options/{$request->option_id}", [
            'value' => $publicUrl,
        ]);

        if (!$response->ok()) {
            return back()->with('error', 'Failed to update queue video setting.');
        }

        return back()->with('success', 'Queue video updated successfully.');

    } catch (\Exception $e) {
        Log::error('Queue video update error', ['error' => $e->getMessage()]);
        return back()->with('error', 'Service unavailable');
    }
}



public function updateLogo(Request $request, SettingService $settings)
{
    $request->validate([
        'setting_id' => 'required|integer',
        'option_id'  => 'required|integer',
        'logo'       => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
    ]);

    try {
        $file = $request->file('logo');

        $disk = \Storage::disk('public');
        $directory = 'images/logo';
        foreach ($disk->files($directory) as $existing) {
            $disk->delete($existing);
        }

        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs($directory, $originalName, 'public');

        $publicUrl = asset('storage/' . $path);

        $response = $this->api->put("/settings/{$request->setting_id}/options/{$request->option_id}", [
            'value' => $publicUrl,
        ]);

        if (!$response->ok()) {
            return back()->with('error', 'Failed to update logo setting.');
        }

        // Clear cached logo so sidebar picks up new URL
        $settings->clearCache('logo');

        return back()->with('success', 'Logo updated successfully.');

    } catch (\Exception $e) {
        Log::error('Logo update error', ['error' => $e->getMessage()]);
        return back()->with('error', 'Service unavailable.');
    }
}


}
