<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function show($userId)
    {
        $profileResponse = $this->api->get("/profiles/{$userId}");
        Log::info("Profile API response", ['userId' => $userId, 'response' => $profileResponse->body()]);
        $userResponse = $this->api->get("/users/{$userId}");
        $user = $userResponse->successful() ? $userResponse->json() : null;

        $name = $user['full_name'] ?? 'User Name';
        $profile = $profileResponse->successful() ? $profileResponse->json() : null;
        $filename = $profile['photo_url'] ?? null;
        $photoUrl = $filename
            ? asset('storage/profile_photos/' . $filename)
            : 'https://ui-avatars.com/api/?name=User&size=150';
        $leaveResponse = $this->api->get("/leave-requests");
        Log::info("Leave requests API response", ['response' => $leaveResponse->body()]);
        
        $leaveRequests = $leaveResponse->successful() ? $leaveResponse->json() : [];

        if (!$profile) {
            session()->flash('error', 'Unable to fetch profile data.');
        } else {
            // Log the photo field specifically
            Log::info("Profile photo URL", ['photo' => $profile['photo_url'] ?? null]);
        }
        $theme = session('theme', $profile['theme'] ?? 'light');
        Log::info("photoUrl computed", ['photoUrl' => $photoUrl]);
        return view('profiles.show', compact('profile', 'leaveRequests', 'theme', 'name', 'photoUrl'));
    }

public function update(Request $request, $userId)
{
    Log::info("Update called", ['userId' => $userId, 'requestKeys' => array_keys($request->all())]);


    $data = $request->except('_token', '_method');


    // Fetch current profile from API
    $profileResponse = $this->api->get("/profiles/{$userId}");
    $profile = $profileResponse->successful() ? $profileResponse->json() : null;


    if (!$profile) {
        Log::error("Profile not found for userId {$userId}");
        return back()->withErrors(['error' => 'Profile not found.']);
    }


    // Handle photo upload: Laravel storage ONLY
    if ($request->hasFile('photo_file')) {
        $file = $request->file('photo_file');
        Log::info("Photo detected in request", [
            'originalName' => $file->getClientOriginalName(),
            'mimeType'     => $file->getMimeType(),
            'size'         => $file->getSize(),
        ]);


        // user-specific filename, e.g. user_5.png
        $extension = $file->getClientOriginalExtension();
        $filename  = 'user_' . $userId . '.' . $extension;


        // store in storage/app/public/profile_photos/user_X.ext
        $path = $file->storeAs('profile_photos', $filename, 'public');
        Log::info("Photo stored at", ['path' => $path]);


        // send ONLY the filename to FastAPI (not a full URL)
        // make sure FastAPI's ProfileUpdate has photo_url: str | None
        $data['photo_url'] = $filename;
        Log::info("Photo filename set for API update", ['photo_url' => $data['photo_url']]);
    } else {
        Log::warning("No photo found in request");
    }


    // Handle goals/checklist/theme as before...
    if (!empty($request->goals)) {
        $data['goals'] = array_map('trim', explode(',', $request->goals));
    }


    if (!empty($request->checklist)) {
        $data['checklist'] = array_map('trim', explode(',', $request->checklist));
    }


    if (isset($data['theme'])) {
        session(['theme' => $data['theme']]);
    }


    // Update profile in FastAPI
    $updateResponse = $this->api->put("/profiles/{$userId}", $data);
    Log::info("Profile update API response", [
        'userId'   => $userId,
        'response' => $updateResponse->body(),
    ]);


    return back()->with('success', 'Profile updated successfully!');
}


}
