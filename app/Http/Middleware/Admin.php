<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Not used in this code


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
     //   $admin  get all data from admins table then get the

        if ($request->user() && $request->user()->role == "Principal") {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}