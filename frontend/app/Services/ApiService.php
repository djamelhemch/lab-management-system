<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = env('FASTAPI_URL', 'http://127.0.0.1:8000');

        if (empty($this->baseUrl)) {
            throw new \Exception('FASTAPI_URL environment variable is not set.');
        }

        $this->token = session('token'); // Grab token from session
    }

    protected function client()
    {
        $client = Http::baseUrl($this->baseUrl);

        if ($this->token) {
            $client = $client->withToken($this->token);
        }

        return $client;
    }

    public function get($endpoint, $params = [])
    {
        return $this->client()->get($endpoint, $params);
    }

    public function post($endpoint, $data)
    {
        return $this->client()->post($endpoint, $data);
    }

    public function put($endpoint, $data)
    {
        return $this->client()->put($endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->client()->delete($endpoint);
    }
}
