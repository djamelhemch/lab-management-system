<?php

// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->get('authUser');

        if ($user && isset($user['role']) && $user['role'] === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}