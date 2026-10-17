<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please log in again.',
                ], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}