<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles):Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non authentifié'
            ], 401);
        }

        // Récupérer le rôle de l'utilisateur
        $userRole = $request->user()->role->role_name;

        // Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return $next($request);
    }
}