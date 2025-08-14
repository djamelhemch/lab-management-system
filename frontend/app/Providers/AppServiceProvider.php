<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
        $authUser = null;
        $token = Session::get('token');

        if ($token) {
            try {
                $response = Http::withToken($token)
                    ->get(env('FASTAPI_URL') . '/users/me');

                if ($response->ok()) {
                    $user = $response->json();

                    // Fetch the profile for this user
                    $profileResponse = Http::withToken($token)
                        ->get(env('FASTAPI_URL') . '/profiles/' . $user['id']);

                    if ($profileResponse->ok()) {
                        $profile = $profileResponse->json();
                        $user['photo_url'] = $profile['photo_url'] ?? null;
                    } else {
                        $user['photo_url'] = null;
                    }

                    $authUser = $user;
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching auth user: ' . $e->getMessage());  
            }
        }

        $view->with('authUser', $authUser);
    });

    }

}
