<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
   
    public function handle($request, Closure $next)
    {
        $token = $request->header('X-API-Token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Token manquant'
            ], 401);
        }

        // Vérifier si le token est valide
        if (!$this->isValidToken($token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Token invalide'
            ], 401);
        }

        return $next($request);
    }

    protected function isValidToken($token)
    {
        // Implémenter votre logique de validation de token
        // Par exemple, vérifier dans la base de données ou contre une liste blanche
        $validTokens = config('api.valid_tokens', []);
        return in_array($token, $validTokens);
    }
}
