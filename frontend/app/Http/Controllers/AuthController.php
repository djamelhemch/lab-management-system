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
        $this->apiBaseUrl = env('FASTAPI_BASE_URL', 'http://localhost:8000');
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
            Session::put('token', $data['access_token']);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout()
    {
        Session::forget('token');
        return redirect()->route('login');
    }
}
