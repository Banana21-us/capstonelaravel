<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class Online
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next): Response
{
    Log::info('Middleware triggered for URL: ' . $request->url());
    $token = $request->bearerToken();
    Log::info('Token received', ['token' => $token]);
    if (!$token) {
        Log::warning('Unauthorized access attempt: No token provided.', ['user' => null]);
        return response()->json(['message' => 'Unauthorized: No token provided'], 401);
    }
    $admin = Admin::where('api_token', $token)->first();
    Log::info('Authenticated user', ['user' => $admin]);
    if ($admin && ($admin->role === 'Teacher' || $admin->role === 'Admin')) {
        return $next($request);
    }
    Log::warning('Unauthorized access attempt', ['user' => $admin]);
    return response()->json(['message' => 'Unauthorized'], 401);
}


}
