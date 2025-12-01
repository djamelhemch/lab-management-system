<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
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

            // Fetch comprehensive financial stats
            $revenueResponse = Http::get($this->apiBaseUrl . '/quotations/stats');
            $revenueStats = $revenueResponse->successful() ? $revenueResponse->json() : [];

            // Fetch predictive analytics
            $predictionsResponse = Http::get($this->apiBaseUrl . '/analytics/predictions');
            $predictions = $predictionsResponse->successful() ? $predictionsResponse->json() : [];

            // Fetch quotations for analysis
            $quotationsResponse = Http::get($this->apiBaseUrl . '/quotations', ['page' => 1, 'limit' => 100]);
            $quotationsData = $quotationsResponse->successful() ? $quotationsResponse->json() : [];
            $quotations = collect($quotationsData['items'] ?? []);

            // Extract stats
            $paidStats = $revenueStats['paid'] ?? [];
            $outstandingStats = $revenueStats['outstanding'] ?? [];

            // Calculate KPIs
            $totalQuotations = $quotationsData['total'] ?? 0;
            $pendingQuotations = $quotations->where('status', 'pending')->count();
            $convertedQuotations = $quotations->where('status', 'converted')->count();
            $averageQuotationValue = $quotations->avg('net_total') ?? 0;
            $conversionRate = $totalQuotations > 0 ? ($convertedQuotations / $totalQuotations * 100) : 0;

            return view('statistics.index', [
                // Core metrics
                'patientsCount' => $metrics['patients_count'] ?? 0,
                'doctorsCount' => $metrics['doctors_count'] ?? 0,
                'samplesToday' => $metrics['samples_today'] ?? 0,
                'pendingReports' => $metrics['pending_reports'] ?? 0,
                'receptionQueueCount' => $metrics['reception_queue_count'] ?? 0,
                'bloodDrawQueueCount' => $metrics['blood_draw_queue_count'] ?? 0,
                'quotationsCount' => $totalQuotations,
                
                // Financial metrics - Paid Revenue
                'paidToday' => $paidStats['today'] ?? 0,
                'paidWeek' => $paidStats['week'] ?? 0,
                'paidMonth' => $paidStats['month'] ?? 0,
                'paidYear' => $paidStats['year'] ?? 0,
                'paidAllTime' => $paidStats['all_time'] ?? 0,
                
                // Financial metrics - Outstanding Balance
                'outstandingToday' => $outstandingStats['today'] ?? 0,
                'outstandingWeek' => $outstandingStats['week'] ?? 0,
                'outstandingMonth' => $outstandingStats['month'] ?? 0,
                'outstandingYear' => $outstandingStats['year'] ?? 0,
                'outstandingAllTime' => $outstandingStats['all_time'] ?? 0,
                
                // KPIs
                'totalQuotations' => $totalQuotations,
                'pendingQuotations' => $pendingQuotations,
                'convertedQuotations' => $convertedQuotations,
                'averageQuotationValue' => $averageQuotationValue,
                'conversionRate' => $conversionRate,
                
                // Predictive Analytics
                'predictions' => $predictions,
                'revenueStats' => $revenueStats
            ]);

        } catch (\Exception $e) {
            Log::error('Statistics error: ' . $e->getMessage());
            
            return view('statistics.index', $this->getDefaultData())
                ->withErrors(['error' => 'Failed to fetch statistics data.']);
        }
    }

    public function stats()
    {
        try {
            $metricsResponse = Http::get($this->apiBaseUrl . '/dashboard/metrics');
            $metrics = $metricsResponse->successful() ? $metricsResponse->json() : [];

            return response()->json([
                'patientsCount' => $metrics['patients_count'] ?? 0,
                'doctorsCount' => $metrics['doctors_count'] ?? 0,
                'samplesToday' => $metrics['samples_today'] ?? 0,
                'pendingReports' => $metrics['pending_reports'] ?? 0,
                'receptionQueueCount' => $metrics['reception_queue_count'] ?? 0,
                'bloodDrawQueueCount' => $metrics['blood_draw_queue_count'] ?? 0,
                'quotationsCount' => $metrics['quotations_count'] ?? 0,
            ]);
        } catch (\Exception $e) {
            return response()->json($this->getDefaultData(), 500);
        }
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
            'paidToday' => 0,
            'paidWeek' => 0,
            'paidMonth' => 0,
            'paidYear' => 0,
            'paidAllTime' => 0,
            'outstandingToday' => 0,
            'outstandingWeek' => 0,
            'outstandingMonth' => 0,
            'outstandingYear' => 0,
            'outstandingAllTime' => 0,
            'totalQuotations' => 0,
            'pendingQuotations' => 0,
            'convertedQuotations' => 0,
            'averageQuotationValue' => 0,
            'conversionRate' => 0,
            'predictions' => [],
            'revenueStats' => []
        ];
    }
}
