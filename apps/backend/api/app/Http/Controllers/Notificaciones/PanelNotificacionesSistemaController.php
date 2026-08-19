<?php

namespace App\Http\Controllers\Notificaciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Notificaciones\Request\CrearNotificacionSistemaRequest;
use App\Http\Controllers\Notificaciones\Services\NotificacionesSistemaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PanelNotificacionesSistemaController extends Controller
{
    public function __construct(protected NotificacionesSistemaService $notificacionesService) {}

    public function listar(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query('limit', 80);
            $limit = $limit > 0 ? min($limit, 200) : 80;

            $data = $this->notificacionesService->listarNotificacionesSistema((int) $request->user()->id, $limit);

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al listar notificaciones del sistema.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function marcarTodasLeidas(Request $request): JsonResponse
    {
        try {
            $data = $this->notificacionesService->marcarTodasComoLeidas((int) $request->user()->id);

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al marcar notificaciones como leidas.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function marcarLeida(int $notificacionId, Request $request): JsonResponse
    {
        try {
            $data = $this->notificacionesService->marcarComoLeida($notificacionId, (int) $request->user()->id);
            $status = !empty($data['ok']) ? 200 : 404;

            return response()->json($data, $status);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al marcar la notificacion como leida.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function crear(CrearNotificacionSistemaRequest $request): JsonResponse
    {
        try {
            $data = $this->notificacionesService->crearNotificacion($request->validated(), (int) $request->user()->id);

            return response()->json($data, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la notificacion.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
