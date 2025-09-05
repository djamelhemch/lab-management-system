<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Services\ApiService;

class LogsController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        try {
            $query = request()->only(['user_id', 'action_type', 'page', 'per_page']);
            $response = $this->api->get('/logs', $query);

            if ($response->successful()) {
                $json = $response->json();
                $logs = $json['data'] ?? [];
                $pagination = $json['pagination'] ?? null;
            } else {
                $logs = [];
                $pagination = null;
            }

            $userResponse = $this->api->get('/users');
            $users = $userResponse->successful() ? ($userResponse->json()['data'] ?? []) : [];

        } catch (\Exception $e) {
            Log::error('Error fetching logs: ' . $e->getMessage());
            $logs = [];
            $users = [];
            $pagination = null;
        }

        return view('admin.logs', compact('logs', 'users', 'pagination'));
    }

}
