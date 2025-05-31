<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Sample;
use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{

public function index()
{
        $apiBaseUrl = env('FASTAPI_BASE_URL', 'http://localhost:8000');

        try {
            $response = Http::get($apiBaseUrl . '/dashboard/metrics');

            if ($response->successful()) {
                $metrics = $response->json();

                return view('dashboard', [
                    'patientsCount' => $metrics['patients_count'] ?? 0,
                    'doctorsCount' => $metrics['doctors_count'] ?? 0,
                    'samplesToday' => $metrics['samples_today'] ?? 0,
                ]);
            } else {
                Log::error('Dashboard API failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Dashboard API error: ' . $e->getMessage());
        }

        // If API fails, still return the view with fallback values
        return view('dashboard', [
            'patientsCount' => 0,
            'doctorsCount' => 0,
            'samplesToday' => 0,
        ])->withErrors(['error' => 'Failed to fetch dashboard metrics.']);
    }

}

