<?php

namespace App\Http\Controllers\Notificaciones\Services;

use App\Http\Controllers\Notificaciones\Repositories\NotificacionesSistemaRepository;
use App\Models\Empresas\Empresa;
use Carbon\Carbon;

class NotificacionesSistemaService
{
    public function __construct(protected NotificacionesSistemaRepository $notificacionesRepository) {}

    public function listarNotificacionesSistema(int $userId, int $limit = 80): array
    {
        $this->registrarAlertasSuscripciones();

        $items = $this->notificacionesRepository->listarParaSuperadmin($userId, $limit);

        return [
            'items' => $items,
            'resumen' => [
                'total' => $items->count(),
                'no_leidas' => $items->where('leida', 0)->count(),
            ],
        ];
    }

    public function marcarComoLeida(int $notificacionId, int $userId): array
    {
        $notificacion = $this->notificacionesRepository->obtenerPorId($notificacionId);

        if (!$notificacion) {
            return [
                'mensaje' => 'Notificacion no encontrada.',
                'ok' => false,
            ];
        }

        $this->notificacionesRepository->marcarComoLeida($notificacionId, $userId);

        return [
            'mensaje' => 'Notificacion marcada como leida.',
            'ok' => true,
        ];
    }

    public function marcarTodasComoLeidas(int $userId): array
    {
        $total = $this->notificacionesRepository->marcarTodasComoLeidas($userId);

        return [
            'mensaje' => 'Notificaciones actualizadas.',
            'total_actualizadas' => $total,
        ];
    }

    public function crearNotificacion(array $data, ?int $actorId = null): array
    {
        $payload = [
            'evento' => $data['evento'],
            'tipo' => $data['tipo'] ?? 'sistema',
            'severidad' => $data['severidad'] ?? 'info',
            'titulo' => $data['titulo'],
            'mensaje' => $data['mensaje'],
            'icono' => $data['icono'] ?? null,
            'empresa_id' => $data['empresa_id'] ?? null,
            'usuario_actor_id' => $data['usuario_actor_id'] ?? $actorId,
            'destino_modulo' => $data['destino_modulo'] ?? 'notificaciones-sistema',
            'destino_id' => $data['destino_id'] ?? null,
            'destino_payload' => $data['destino_payload'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'hash_evento' => $data['hash_evento'] ?? null,
        ];

        $notificacion = $this->notificacionesRepository->crear($payload);

        return [
            'mensaje' => 'Notificacion registrada correctamente.',
            'item' => $notificacion,
        ];
    }

    public function registrarEvento(string $evento, array $contexto = []): void
    {
        $plantilla = $this->resolverPlantilla($evento, $contexto);

        if (!$plantilla) {
            return;
        }

        $this->crearNotificacion(array_merge($plantilla, [
            'empresa_id' => $contexto['empresa_id'] ?? null,
            'usuario_actor_id' => $contexto['usuario_actor_id'] ?? null,
            'destino_modulo' => $contexto['destino_modulo'] ?? 'notificaciones-sistema',
            'destino_id' => $contexto['destino_id'] ?? null,
            'destino_payload' => $contexto['destino_payload'] ?? null,
            'metadata' => $contexto,
            'hash_evento' => $contexto['hash_evento'] ?? null,
        ]), $contexto['usuario_actor_id'] ?? null);
    }

    private function registrarAlertasSuscripciones(): void
    {
        $hoy = Carbon::today();

        $empresas = Empresa::query()
            ->whereNotNull('fecha_vencimiento')
            ->with('estado:id,nombre')
            ->get();

        foreach ($empresas as $empresa) {
            $estadoNombre = strtolower((string) ($empresa->estado?->nombre ?? ''));

            if ($estadoNombre && $estadoNombre !== 'activo') {
                continue;
            }

            $vencimiento = Carbon::parse($empresa->fecha_vencimiento)->startOfDay();
            $dias = $hoy->diffInDays($vencimiento, false);

            if ($dias >= 0 && $dias <= 7) {
                $hash = sprintf(
                    'suscripcion-proxima-%d-%s-%s',
                    $empresa->id,
                    $vencimiento->toDateString(),
                    $hoy->toDateString()
                );

                $this->registrarEvento('suscripcion_proxima_vencer', [
                    'empresa_id' => $empresa->id,
                    'empresa_nombre' => $empresa->nombre_comercial,
                    'dias_restantes' => $dias,
                    'fecha_vencimiento' => $vencimiento->toDateString(),
                    'hash_evento' => $hash,
                    'destino_modulo' => 'notificaciones-sistema',
                    'destino_id' => (string) $empresa->id,
                ]);
            }

            if ($dias < 0) {
                $hash = sprintf('suscripcion-vencida-%d-%s', $empresa->id, $vencimiento->toDateString());

                $this->registrarEvento('suscripcion_vencida', [
                    'empresa_id' => $empresa->id,
                    'empresa_nombre' => $empresa->nombre_comercial,
                    'dias_vencida' => abs($dias),
                    'fecha_vencimiento' => $vencimiento->toDateString(),
                    'hash_evento' => $hash,
                    'destino_modulo' => 'notificaciones-sistema',
                    'destino_id' => (string) $empresa->id,
                ]);
            }
        }
    }

    private function resolverPlantilla(string $evento, array $contexto = []): ?array
    {
        $empresaNombre = $contexto['empresa_nombre'] ?? 'Empresa';
        $usuarioNombre = $contexto['usuario_nombre'] ?? 'Usuario';

        switch ($evento) {
            case 'empresa_registrada':
                return [
                    'evento' => $evento,
                    'severidad' => 'success',
                    'icono' => 'mdi-domain-plus',
                    'titulo' => 'Nueva empresa registrada',
                    'mensaje' => "Se registro la empresa {$empresaNombre}.",
                ];
            case 'usuario_creado':
                return [
                    'evento' => $evento,
                    'severidad' => 'info',
                    'icono' => 'mdi-account-plus',
                    'titulo' => 'Usuario creado',
                    'mensaje' => "Se creo el usuario {$usuarioNombre} en {$empresaNombre}.",
                ];
            case 'pago_realizado':
                return [
                    'evento' => $evento,
                    'severidad' => 'success',
                    'icono' => 'mdi-credit-card-check-outline',
                    'titulo' => 'Pago confirmado',
                    'mensaje' => "Se recibio un pago de {$empresaNombre} por {$contexto['valor']}. Referencia: {$contexto['referencia']}.",
                ];
            case 'suscripcion_proxima_vencer':
                return [
                    'evento' => $evento,
                    'severidad' => 'warning',
                    'icono' => 'mdi-clock-alert-outline',
                    'titulo' => 'Suscripcion proxima a vencer',
                    'mensaje' => "La suscripcion de {$empresaNombre} vence en {$contexto['dias_restantes']} dia(s). Fecha: {$contexto['fecha_vencimiento']}.",
                ];
            case 'suscripcion_vencida':
                return [
                    'evento' => $evento,
                    'severidad' => 'error',
                    'icono' => 'mdi-calendar-remove-outline',
                    'titulo' => 'Suscripcion vencida',
                    'mensaje' => "La suscripcion de {$empresaNombre} esta vencida desde {$contexto['fecha_vencimiento']}.",
                ];
            case 'empresa_bloqueada':
                return [
                    'evento' => $evento,
                    'severidad' => 'error',
                    'icono' => 'mdi-account-cancel-outline',
                    'titulo' => 'Empresa bloqueada',
                    'mensaje' => "La empresa {$empresaNombre} fue bloqueada por una accion administrativa.",
                ];
            case 'solicitud_aprobacion':
                return [
                    'evento' => $evento,
                    'severidad' => 'warning',
                    'icono' => 'mdi-file-sign',
                    'titulo' => 'Solicitud pendiente de aprobacion',
                    'mensaje' => $contexto['mensaje'] ?? "{$empresaNombre} envio una solicitud que requiere aprobacion.",
                ];
            case 'error_importante':
                return [
                    'evento' => $evento,
                    'severidad' => 'critical',
                    'icono' => 'mdi-alert-octagon-outline',
                    'titulo' => 'Error importante detectado',
                    'mensaje' => $contexto['mensaje'] ?? 'Se detecto un error importante en LogistikPro.',
                ];
            case 'evento_seguridad':
                return [
                    'evento' => $evento,
                    'severidad' => 'critical',
                    'icono' => 'mdi-shield-alert-outline',
                    'titulo' => 'Evento de seguridad',
                    'mensaje' => $contexto['mensaje'] ?? 'Se detecto un evento de seguridad relevante.',
                ];
            case 'accion_administrativa':
                return [
                    'evento' => $evento,
                    'severidad' => 'info',
                    'icono' => 'mdi-cog-outline',
                    'titulo' => 'Accion administrativa',
                    'mensaje' => $contexto['mensaje'] ?? 'Se ejecuto una accion administrativa importante.',
                ];
            default:
                return null;
        }
    }
}
