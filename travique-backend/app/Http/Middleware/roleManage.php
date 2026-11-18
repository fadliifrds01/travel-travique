<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class roleManage
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === "admin") {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => "Anda tidak memiliki akses untuk halaman ini"
        ], 403);
    }
}
