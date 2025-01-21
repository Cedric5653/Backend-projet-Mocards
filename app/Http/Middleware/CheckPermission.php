<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non authentifié'
            ], 401);
        }

        // Utilisation du cache pour les permissions
        $cacheKey = 'user_permissions_' . $request->user()->user_id;
        
        $userPermissions = Cache::remember($cacheKey, 3600, function () use ($request) {
            return $request->user()
                ->role()
                ->with('permissions')
                ->first()
                ->permissions
                ->pluck('permission_name')
                ->toArray();
        });

        if (!in_array($permission, $userPermissions)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission refusée'
            ], 403);
        }

        return $next($request);
    }
}



