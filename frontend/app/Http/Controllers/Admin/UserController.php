<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            // Just pass query params, no need to pass headers here.
            $response = $this->api->get('/users');

            if ($response->successful()) {
                $json = $response->json();
                $users = $json['data'] ?? $json;
            } else {
                $users = [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
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

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:admin,biologist,technician,secretary,intern',
            'password' => 'required|confirmed|min:8',
            'status' => 'required|in:active,inactive',
        ]);

        Log::info('Creating user', ['data' => $validated]);

        try {
            $response = $this->api->post('/users', $validated);

            if ($response->successful()) {
                Log::info('User created successfully');
                return redirect()->route('admin.users.index')
                    ->with('success', 'User created successfully');
            }

            Log::error('User creation failed', ['response' => $response->json()]);
            return back()->withErrors($response->json()['detail'] ?? 'Creation failed');
        } catch (\Exception $e) {
            Log::error('User creation error', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Service unavailable');
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
                return view('admin.users.edit', compact('user'));
            }

            abort(404, 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            abort(500, 'Internal server error.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:admin,biologist,technician,secretary,intern',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:255',
            'password' => 'nullable|confirmed|min:6',
        ]);

        try {
            $response = $this->api->put("/users/{$id}", $validated);

            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User updated successfully.');
            }

            return back()->withErrors(['error' => 'Update failed'])->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server error'])->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $response = $this->api->delete("/users/{$user->id}");

            if ($response->successful()) {
                $user->delete(); // Optional: remove from Laravel too
                return back()->with('success', 'User deleted.');
            }

            return back()->withErrors(['error' => 'Failed to delete user.']);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Server error.']);
        }
    }
}
