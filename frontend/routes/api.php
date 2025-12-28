<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/lab-formulas', function () {
    $response = Http::timeout(3)->get(
        rtrim(config('services.fastapi.url'), '/') . '/lab-formulas'
    );

    if (!$response->successful()) {
        return response()->json([
            'error' => 'FastAPI unreachable'
        ], 502);
    }

    return response()->json($response->json());
});
