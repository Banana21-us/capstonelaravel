<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;



class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle(Request $request, Closure $next): Response
{
    // Retrieve the token from the request
    $token = $request->bearerToken();

    // Log the token being sent in the request
    Log::info('Token', ['token!!' => $token]);

    // Check if the token is present
    if (!$token) {
        Log::warning('Unauthorized access attempt: No token provided.', ['user' => null]);
        return response()->json(['message' => 'Unauthorized: No token provided'], 401);
    }

    // Attempt to authenticate the user
    $user = auth('admins')->user();

    // Log user attempting to access
    Log::info('User attempting access', ['user!!' => $user]);

    // Check if user exists and has the appropriate role
    if ($user && ($user->role === 'Teacher' || $user->role === 'Admin')) {
        return $next($request);
    }

    // Log unauthorized access attempt
    Log::warning('Unauthorized access attempt', ['user' => $user]);

    return response()->json(['message' => 'Unauthorized'], 401);
}

     
     
}