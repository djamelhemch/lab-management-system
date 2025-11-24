<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('FASTAPI_URL', 'http://localhost:8000');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $response = Http::asForm()->post($this->apiBaseUrl . '/token', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if ($response->ok()) {
            $data = $response->json();
            $token = $data['access_token'];

            // ✅ Store token
            Session::put('token', $token);

            // ✅ Fetch user info from /users/me using token
            $userResponse = Http::withToken($token)->get($this->apiBaseUrl . '/users/me');

            if ($userResponse->ok()) {
                $user = $userResponse->json();

                // ✅ Store user info in session
                Session::put('user', $user);
                Session::put('role', $user['role']); // for convenience

                return redirect()->route('dashboard');
            }
            if ($response->status() === 401) {
                Session::forget(['token', 'user', 'role']);
                return redirect()->route('login')->withErrors(['session' => 'Session expired, please log in again.']);
            }
            // If token works but /users/me fails (very rare)
            return redirect()->route('login')->withErrors(['username' => 'Login succeeded, but user info failed']);
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout()
    {
        $token = Session::get('token');

        if ($token) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])->post($this->apiBaseUrl . '/logout');

                // Optionally you can check $response->successful() here
            } catch (\Exception $e) {
                Log::error('Logout API request failed: ' . $e->getMessage());
            }
        }

        Session::forget('token');
        Session::forget('user');
        Session::forget('role');

        return redirect()->route('login');
    }

}
