<?php

namespace App\Http\Controllers\Notificaciones\Repositories;

use App\Models\Notificaciones\NotificacionSistema;
use App\Models\Notificaciones\NotificacionSistemaLectura;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificacionesSistemaRepository
{
    public function crear(array $data): NotificacionSistema
    {
        $hashEvento = $data['hash_evento'] ?? null;

        if ($hashEvento) {
            $existente = NotificacionSistema::query()
                ->where('hash_evento', $hashEvento)
                ->first();

            if ($existente) {
                return $existente;
            }
        }

        return NotificacionSistema::query()->create($data);
    }

    public function listarParaSuperadmin(int $userId, int $limit = 80): Collection
    {
        return NotificacionSistema::query()
            ->leftJoin('notificaciones_sistema_lecturas as lecturas', function ($join) use ($userId) {
                $join->on('lecturas.notificacion_id', '=', 'notificaciones_sistema.id')
                    ->where('lecturas.user_id', '=', $userId);
            })
            ->leftJoin('empresas', 'empresas.id', '=', 'notificaciones_sistema.empresa_id')
            ->leftJoin('users as actor', 'actor.id', '=', 'notificaciones_sistema.usuario_actor_id')
            ->select(
                'notificaciones_sistema.id',
                'notificaciones_sistema.evento',
                'notificaciones_sistema.tipo',
                'notificaciones_sistema.severidad',
                'notificaciones_sistema.titulo',
                'notificaciones_sistema.mensaje',
                'notificaciones_sistema.icono',
                'notificaciones_sistema.empresa_id',
                'notificaciones_sistema.usuario_actor_id',
                'notificaciones_sistema.destino_modulo',
                'notificaciones_sistema.destino_id',
                'notificaciones_sistema.destino_payload',
                'notificaciones_sistema.metadata',
                'notificaciones_sistema.created_at',
                'notificaciones_sistema.updated_at',
                'empresas.nombre_comercial as empresa_nombre',
                DB::raw("TRIM(CONCAT(COALESCE(actor.nombre, ''), ' ', COALESCE(actor.apellido, ''))) as actor_nombre"),
                DB::raw('CASE WHEN lecturas.id IS NULL THEN 0 ELSE 1 END as leida'),
                'lecturas.leida_at'
            )
            ->orderByDesc('notificaciones_sistema.created_at')
            ->orderByDesc('notificaciones_sistema.id')
            ->limit($limit)
            ->get();
    }

    public function marcarComoLeida(int $notificacionId, int $userId): NotificacionSistemaLectura
    {
        return NotificacionSistemaLectura::query()->updateOrCreate(
            [
                'notificacion_id' => $notificacionId,
                'user_id' => $userId,
            ],
            [
                'leida_at' => Carbon::now(),
            ]
        );
    }

    public function marcarTodasComoLeidas(int $userId): int
    {
        $ahora = Carbon::now();

        $notificacionesSinLeer = NotificacionSistema::query()
            ->whereDoesntHave('lecturas', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->pluck('id')
            ->all();

        if (empty($notificacionesSinLeer)) {
            return 0;
        }

        $rows = array_map(function ($notificacionId) use ($userId, $ahora) {
            return [
                'notificacion_id' => $notificacionId,
                'user_id' => $userId,
                'leida_at' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }, $notificacionesSinLeer);

        return NotificacionSistemaLectura::query()->insertOrIgnore($rows);
    }

    public function obtenerPorId(int $notificacionId): ?NotificacionSistema
    {
        return NotificacionSistema::query()->find($notificacionId);
    }
}
