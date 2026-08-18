<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['message' => 'Unauthenticated'], 401);

        $admin = Admin::where('api_token', $token)->where('is_active', true)->first();
        if (!$admin) return response()->json(['message' => 'Unauthenticated'], 401);

        $request->attributes->add(['admin' => $admin]);
        return $next($request);
    }
}