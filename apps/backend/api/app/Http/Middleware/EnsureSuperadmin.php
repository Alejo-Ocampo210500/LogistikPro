<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->rol) {
            $rolNombre = strtolower((string) $user->rol->nombre);
            if ($rolNombre === 'superadmin' || $rolNombre === 'superadministrador') {
                return $next($request);
            }
        }

        return response()->json([
            'mensaje' => 'No tienes permisos para acceder a esta sección.',
        ], 403);
    }
}
