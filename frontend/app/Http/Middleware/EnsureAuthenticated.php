<?php
// app/Http/Middleware/EnsureAuthenticated.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('token')) {
            return redirect()->route('login');
        }

        $token = session('token');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get(env('FASTAPI_URL') . '/users/me');

            if ($response->failed()) {
                session()->forget('token');
                return redirect()->route('login');
            }

            // Share user info globally
            $user = $response->json();
            view()->share('authUser', $user);
            $request->attributes->set('authUser', $user);

        } catch (\Exception $e) {
            session()->forget('token');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
