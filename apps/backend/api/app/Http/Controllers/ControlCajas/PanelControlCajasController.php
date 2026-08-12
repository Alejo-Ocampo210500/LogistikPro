<?php

namespace App\Http\Controllers\ControlCajas;

use App\Http\Controllers\ControlCajas\Repositories\ControlCajasRepository;
use App\Http\Controllers\ControlCajas\Request\ControlCajaRequest;
use App\Http\Controllers\Controller;

class PanelControlCajasController extends Controller
{
    public function __construct(protected ControlCajasRepository $ControlCajasRepository) {}

    protected function esAdministradorAutenticado(): bool
    {
        $roleName = strtolower((string) (request()->user()?->rol?->nombre ?? ''));
        return str_contains($roleName, 'admin');
    }

    public function obtenerControlCajasCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $controlCajas = $this->ControlCajasRepository->obtenerControlCajasCliente($empresaId);

            return response()->json([
                'mensaje' => 'Control de cajas obtenidos correctamente.',
                'controlCajas' => $controlCajas,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener el control de cajas.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function abrirControlCajaCliente(ControlCajaRequest $request)
    {
        try {
            $user = request()->user();
            $empresaId = (int) ($user?->empresa_id ?? 0);
            $userId = (int) ($user?->id ?? 0);

            if ($empresaId <= 0 || $userId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver el usuario o la empresa autenticada.',
                ], 422);
            }

            if (!$this->esAdministradorAutenticado()) {
                $tieneAbierta = $this->ControlCajasRepository->usuarioTieneCajaAbierta($empresaId, $userId);

                if ($tieneAbierta) {
                    return response()->json([
                        'mensaje' => 'Solo puedes tener una caja abierta a la vez.',
                    ], 422);
                }
            }

            $controlCaja = $this->ControlCajasRepository->abrirControlCajaCliente($empresaId, $userId, $request->validated());

            return response()->json([
                'mensaje' => 'Caja abierta correctamente.',
                'controlCaja' => $controlCaja,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al abrir la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cerrarControlCajaCliente(int $controlCajaId, ControlCajaRequest $request)
    {
        try {
            $user = request()->user();
            $empresaId = (int) ($user?->empresa_id ?? 0);
            $userId = (int) ($user?->id ?? 0);

            if ($empresaId <= 0 || $userId <= 0 || $controlCajaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver el cierre de caja.',
                ], 422);
            }

            $controlCaja = $this->ControlCajasRepository->cerrarControlCajaCliente(
                $controlCajaId,
                $empresaId,
                $userId,
                $request->validated(),
                $this->esAdministradorAutenticado()
            );

            return response()->json([
                'mensaje' => 'Caja cerrada correctamente.',
                'controlCaja' => $controlCaja,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cerrar la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function anularControlCajaCliente(int $controlCajaId, ControlCajaRequest $request)
    {
        try {
            $user = request()->user();
            $empresaId = (int) ($user?->empresa_id ?? 0);
            $userId = (int) ($user?->id ?? 0);

            if ($empresaId <= 0 || $userId <= 0 || $controlCajaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la anulación de caja.',
                ], 422);
            }

            $controlCaja = $this->ControlCajasRepository->anularControlCajaCliente(
                $controlCajaId,
                $empresaId,
                $userId,
                $request->validated(),
                $this->esAdministradorAutenticado()
            );

            return response()->json([
                'mensaje' => 'Caja anulada correctamente.',
                'controlCaja' => $controlCaja,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al anular la caja.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
