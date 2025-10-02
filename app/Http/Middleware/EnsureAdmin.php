<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            return ResponseHelper::jsonResponse(false, 'Anda Tidak Memiliki Akses', null, 403); // ('Unauthorized: Admin access only', null, 403);
        }

        return $next($request);
    }
}
