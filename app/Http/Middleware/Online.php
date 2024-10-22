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

    // Retrieve the token from the request
    $token = $request->bearerToken();
    Log::info('Token received', ['token' => $token]);

    // Check if the token is present
    if (!$token) {
        Log::warning('Unauthorized access attempt: No token provided.', ['user' => null]);
        return response()->json(['message' => 'Unauthorized: No token provided'], 401);
    }

    // Attempt to authenticate the user using the token
    $admin = Admin::where('api_token', $token)->first(); // Make sure your Admin model is imported

    // Log the result of the authentication attempt
    Log::info('Authenticated user', ['user' => $admin]);

    // Check if user exists and has the appropriate role
    if ($admin && ($admin->role === 'Teacher' || $admin->role === 'Admin')) {
        return $next($request);
    }

    // Log unauthorized access attempt
    Log::warning('Unauthorized access attempt', ['user' => $admin]);
    return response()->json(['message' => 'Unauthorized'], 401);
}


}
