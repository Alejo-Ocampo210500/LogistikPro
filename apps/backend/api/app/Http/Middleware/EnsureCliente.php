<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'mensaje' => 'No autenticado.',
            ], 401);
        }

        $rolNombre = strtolower((string) optional($user->rol)->nombre);

        if ($rolNombre === '') {
            return response()->json([
                'mensaje' => 'No tienes un rol asignado para acceder a esta sección.',
            ], 403);
        }

        if ($rolNombre === 'superadmin' || $rolNombre === 'superadministrador') {
            return response()->json([
                'mensaje' => 'No tienes permisos para acceder a esta sección.',
            ], 403);
        }

        return $next($request);
    }
}
