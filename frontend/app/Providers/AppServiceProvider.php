<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\SettingService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class, function () {
            return new SettingService();
        });
    }

    public function boot(SettingService $settings): void
    {
        URL::forceScheme('https');       
        $logoUrl = $settings->get('logo', asset('/images/logo_lab.PNG'));

        // available in ALL views
        View::share('logoUrl', $logoUrl);

       
        View::composer('*', function ($view) {
            $authUser = null;
            $currency = 'DZD'; // ✅ fallback default currency

            $token = Session::get('token');
            $settingService = app(SettingService::class);

            if ($token) {
                try {
                    $response = Http::withToken($token)
                        ->get(env('FASTAPI_URL') . '/users/me');

                    if ($response->ok()) {
                        $user = $response->json();

                        // ✅ Fetch default currency from settings API
                        $currency = $settingService->get('currency', 'DZD');

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

            // ✅ Share globally (always available, even if guest)
            View::share('defaultCurrency', $currency);
            $view->with('authUser', $authUser);
        });
    }
}
