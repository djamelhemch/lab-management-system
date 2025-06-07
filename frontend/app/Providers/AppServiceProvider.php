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
                    $authUser = $response->json();
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching auth user: ' . $e->getMessage());  
            }
        }

        $view->with('authUser', $authUser);
        });
    }
}
