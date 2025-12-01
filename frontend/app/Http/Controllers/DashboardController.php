<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('FASTAPI_URL', 'http://localhost:8000');
    }

    public function index()
    {
        try {
            // Fetch core metrics
            $metricsResponse = Http::get($this->apiBaseUrl . '/dashboard/metrics');
            $metrics = $metricsResponse->successful() ? $metricsResponse->json() : [];

            // Fetch recent patients
            $patientsResponse = Http::get($this->apiBaseUrl . '/dashboard/recent-patients');
            $recentPatients = $patientsResponse->successful() 
                ? collect($patientsResponse->json()) 
                : collect([]);

            // Fetch recent activities from logs (last 20 for dashboard)
            $logsResponse = Http::get($this->apiBaseUrl . '/logs', ['per_page' => 20]);
            $recentActivities = collect([]);
            
            if ($logsResponse->successful()) {
                $logsData = $logsResponse->json();
                $logs = $logsData['data'] ?? [];
                
                $recentActivities = collect($logs)->map(function($log) {
                    return [
                        'description' => $log['description'] ?? 'Activity',
                        'time' => $this->formatLogTime($log['created_at'] ?? now()),
                        'color' => $this->getActivityColor($log['action_type'] ?? 'INFO'),
                        'icon' => $this->getActivityIcon($log['action_type'] ?? 'INFO'),
                        'user' => $log['user_name'] ?? 'System'
                    ];
                });
            }

            // Get recent activities from API (samples, patients)
            $apiActivitiesResponse = Http::get($this->apiBaseUrl . '/dashboard/recent-activities');
            $apiActivities = $apiActivitiesResponse->successful() 
                ? collect($apiActivitiesResponse->json()) 
                : collect([]);

            // Merge both activity sources
            $allActivities = $recentActivities->concat($apiActivities)->take(15);

            // Fetch quick financial summary
            $revenueResponse = Http::get($this->apiBaseUrl . '/quotations/stats');
            $revenueStats = $revenueResponse->successful() ? $revenueResponse->json() : [];

            return view('dashboard', [
                'patientsCount' => $metrics['patients_count'] ?? 0,
                'doctorsCount' => $metrics['doctors_count'] ?? 0,
                'samplesToday' => $metrics['samples_today'] ?? 0,
                'pendingReports' => $metrics['pending_reports'] ?? 0,
                'receptionQueueCount' => $metrics['reception_queue_count'] ?? 0,
                'bloodDrawQueueCount' => $metrics['blood_draw_queue_count'] ?? 0,
                'quotationsCount' => $metrics['quotations_count'] ?? 0,
                'recentPatients' => $recentPatients,
                'recentActivities' => $allActivities,
                
                // Quick financial overview
                'todayRevenue' => $revenueStats['paid']['today'] ?? 0,
                'monthRevenue' => $revenueStats['paid']['month'] ?? 0,
                'outstandingTotal' => $revenueStats['outstanding']['all_time'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            
            return view('dashboard', $this->getDefaultData());
        }
    }

    private function formatLogTime($timestamp)
    {
        try {
            $time = Carbon::parse($timestamp);
            $diff = now()->diffInMinutes($time);
            
            if ($diff < 1) return "À l'instant";
            if ($diff < 60) return "Il y a {$diff}m";
            if ($diff < 1440) return "Il y a " . floor($diff / 60) . "h";
            return "Il y a " . floor($diff / 1440) . "j";
        } catch (\Exception $e) {
            return "Récemment";
        }
    }

    private function getActivityColor($actionType)
    {
        $colors = [
            'CREATE' => 'green',
            'create_quotation' => 'green',
            'create_patient' => 'blue',
            'UPDATE' => 'blue',
            'DELETE' => 'red',
            'LOGIN' => 'indigo',
            'LOGOUT' => 'gray',
            'convert_quotation' => 'yellow',
            'VIEW' => 'purple',
        ];

        return $colors[strtolower($actionType)] ?? 'gray';
    }

    private function getActivityIcon($actionType)
    {
        $icons = [
            'CREATE' => 'fa-plus-circle',
            'create_quotation' => 'fa-file-invoice',
            'create_patient' => 'fa-user-plus',
            'UPDATE' => 'fa-edit',
            'DELETE' => 'fa-trash',
            'LOGIN' => 'fa-sign-in-alt',
            'LOGOUT' => 'fa-sign-out-alt',
            'convert_quotation' => 'fa-check-circle',
            'VIEW' => 'fa-eye',
        ];

        return $icons[strtolower($actionType)] ?? 'fa-circle';
    }

    private function getDefaultData()
    {
        return [
            'patientsCount' => 0,
            'doctorsCount' => 0,
            'samplesToday' => 0,
            'pendingReports' => 0,
            'receptionQueueCount' => 0,
            'bloodDrawQueueCount' => 0,
            'quotationsCount' => 0,
            'recentPatients' => collect([]),
            'recentActivities' => collect([]),
            'todayRevenue' => 0,
            'monthRevenue' => 0,
            'outstandingTotal' => 0,
        ];
    }
}
