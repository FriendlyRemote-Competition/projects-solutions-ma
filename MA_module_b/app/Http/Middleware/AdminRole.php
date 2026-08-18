<?php

namespace App\Http\Middleware;

use Closure;

class AdminRole
{
    public function handle($request, Closure $next, $role)
    {
        $admin = $request->get('admin');
        if ($admin->role !== $role && $admin->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}