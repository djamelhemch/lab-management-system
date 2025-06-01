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
            // Get main metrics
            $response = Http::get($apiBaseUrl . '/dashboard/metrics');
            
            // Get recent activities
            $activitiesResponse = Http::get($apiBaseUrl . '/dashboard/recent-activities');
            
            // Get recent patients
            $patientsResponse = Http::get($apiBaseUrl . '/dashboard/recent-patients');

            if ($response->successful()) {
                $metrics = $response->json();
                $recentActivities = $activitiesResponse->successful() ? $activitiesResponse->json() : [];
                $recentPatients = $patientsResponse->successful() ? $patientsResponse->json() : [];

                return view('dashboard', [
                    'patientsCount' => $metrics['patients_count'] ?? 0,
                    'doctorsCount' => $metrics['doctors_count'] ?? 0,
                    'samplesToday' => $metrics['samples_today'] ?? 0,
                    'pendingReports' => $metrics['pending_reports'] ?? 0,
                    'recentActivities' => $recentActivities,
                    'recentPatients' => $recentPatients,
                ]);
            } else {
                Log::error('Dashboard API failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Dashboard API error: ' . $e->getMessage());
        }

        // If API fails, return fallback data
        return view('dashboard', [
            'patientsCount' => 0,
            'doctorsCount' => 0,
            'samplesToday' => 0,
            'pendingReports' => 0,
            'recentActivities' => $this->getFallbackActivities(),
            'recentPatients' => $this->getFallbackPatients(),
        ])->withErrors(['error' => 'Failed to fetch dashboard metrics.']);
    }

    private function getFallbackActivities()
    {
        return [
            ['description' => 'System started', 'time' => '1 min ago', 'color' => 'blue'],
            ['description' => 'Dashboard loaded', 'time' => 'Just now', 'color' => 'green'],
        ];
    }

    private function getFallbackPatients()
    {
        return [
            [
                'file_number' => 'P001',
                'first_name' => 'Sample',
                'last_name' => 'Patient',
                'doctor_name' => 'Dr. Sample',
                'status' => 'Active'
            ]
        ];
    }
}