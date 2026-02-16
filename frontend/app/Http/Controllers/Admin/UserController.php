<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiService;

class UserController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        try {
            // Fetch all users
            $response = $this->api->get('/users');
            $users = $response->successful() ? ($response->json()['data'] ?? $response->json()) : [];

            // Fetch online user IDs from user_sessions where is_connected = true
            $statusResponse = $this->api->get('/users/online-status');
            $onlineUserIds = [];
            if ($statusResponse->successful()) {
                $statusJson = $statusResponse->json();
                $onlineUserIds = $statusJson['online_user_ids'] ?? [];
            }

            // Add is_connected attribute by checking active sessions
            foreach ($users as &$user) {
                $user['is_connected'] = in_array($user['id'], $onlineUserIds);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching users or online status: ' . $e->getMessage());
            $users = [];
        }

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

public function store(Request $request)
{
    $request->merge([
        'status' => $request->input('status', 'active'),
    ]);

    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255',
        'full_name' => 'required|string|max:255',
        'email' => 'required|email',
        'role' => 'required|in:admin,biologist,technician,secretary,intern',
        'password' => 'required|confirmed|min:8',
        'status' => 'required|in:active,inactive',
    ]);

    // Return validation errors for AJAX
    if ($validator->fails()) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    $validated = $validator->validated();
    Log::info('Creating user', ['data' => $validated]);

    try {
        $response = $this->api->post('/users', $validated);

        if ($response->successful()) {
            Log::info('User created successfully');
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully'
                ]);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully');
        }

        // Handle API errors
        $errorData = $response->json();
        $errorMessage = $errorData['detail'] ?? 'User creation failed';
        
        Log::error('User creation failed', ['response' => $errorData]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], 400);
        }
        
        return back()->withInput()->with('error', $errorMessage);
        
    } catch (\Exception $e) {
        Log::error('User creation error', ['exception' => $e->getMessage()]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Service unavailable. Please try again later.'
            ], 503);
        }
        
        return back()->withInput()->with('error', 'Service unavailable');
    }
}

    public function show($id)
    {
        try {
            $response = $this->api->get("/users/{$id}");

            if ($response->successful()) {
                $user = $response->json();
                return view('admin.users.show', compact('user'));
            }

            abort(404, 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            abort(500, 'Internal server error.');
        }
    }

    public function edit($id)
    {
        try {
            $response = $this->api->get("/users/{$id}");

            if ($response->successful()) {
                $user = $response->json();
                // Pass user data and id separately, or wrap in a User model (recommended)
                return view('admin.users.edit', ['user' => (object) $user, 'userId' => $id]);
            }
            abort(404, 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            abort(500, 'Internal server error.');
        }
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:admin,biologist,technician,secretary,intern',
            'password' => 'nullable|confirmed|min:6',
        ]);
        
        // Prepare data for API - only send fields that should be updated
        $apiData = [
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            // Status defaults to 'active' if not in form
            'status' => $request->input('status', 'active'),
        ];
        
        // Only include password if provided
        if ($request->filled('password')) {
            $apiData['password'] = $validated['password'];
        }
        
        try {
            $response = $this->api->put("/users/{$id}", $apiData);
            
            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User updated successfully.');
            }
            
            return back()->withErrors(['error' => 'Update failed: ' . $response->body()])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server error: ' . $e->getMessage()])
                ->withInput();
        }
    }


    public function destroy($id)  // Use $id instead of User $user
    {
        try {
            // Call API to delete user
            $response = $this->api->delete("/users/{$id}");

            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User deleted successfully.');
            }

            // Handle API error response
            $errorMessage = $response->json()['detail'] ?? 'Failed to delete user.';
            return back()->withErrors(['error' => $errorMessage]);

        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server error occurred while deleting user.']);
        }
    }
}
