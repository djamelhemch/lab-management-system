<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Validator;

class HubController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $user = session('user');

        // Fetch operational data
        $pendingResults   = $this->api->get('lab-results/pending')->json() ?? [];
        $todayQuotations  = $this->api->get('quotations/today')->json() ?? [];
        $analyses         = $this->api->get('analyses')->json() ?? [];

        return view('hub.index', [
            'username'        => $user['username'] ?? 'Utilisateur',
            'pendingCount'    => count($pendingResults),
            'todayQuoteCount' => count($todayQuotations),
            'analysisCount'   => count($analyses),
            'hour'            => now()->hour,
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'pending' => count($this->api->get('lab-results/pending')->json() ?? []),
            'quotes'  => count($this->api->get('quotations/today')->json() ?? []),
        ]);
    }


}