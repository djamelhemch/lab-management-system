<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;
    
    public function __construct()
    {
        
        $this->baseUrl = env('FASTAPI_URL', 'http://127.0.0.1:8000');
        
        $response = Http::timeout(10)->get(env('FASTAPI_URL') . '/patients');

        if (empty($this->baseUrl)) {
            throw new \Exception('FASTAPI_URL environment variable is not set.');
        }
        
    }

    public function get($endpoint)
    {
        return Http::get("{$this->baseUrl}/{$endpoint}");
    }

    public function post($endpoint, $data)
    {
        return Http::post("{$this->baseUrl}/{$endpoint}", $data);
    }

    public function put($endpoint, $data)
    {
        return Http::put("{$this->baseUrl}/{$endpoint}", $data);
    }

    public function delete($endpoint)
    {
        return Http::delete("{$this->baseUrl}/{$endpoint}");
    }
}
