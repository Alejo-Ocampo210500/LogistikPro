<?php

namespace App\Http\Controllers\ControlCajas\Repositories;

use App\Models\ControlCajas\ControlCaja;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ControlCajasRepository
{
    private const BOGOTA_TZ = 'America/Bogota';

    protected function detailRelations(): array
    {
        return [
            'caja:id,sucursal_id,codigo,nombre,descripcion,impresora,estado_id',
            'sucursal:id,nombre',
            'usuarioApertura:id,nombre,apellido',
            'usuarioCierre:id,nombre,apellido',
            'creador:id,nombre,apellido',
            'actualizador:id,nombre,apellido',
        ];
    }

    public function obtenerControlCajasCliente(int $empresaId)
    {
        return ControlCaja::query()
            ->with($this->detailRelations())
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function usuarioTieneCajaAbierta(int $empresaId, int $userId): bool
    {
        return ControlCaja::query()
            ->where('empresa_id', $empresaId)
            ->where('usuario_apertura_id', $userId)
            ->where('estado', 'Abierta')
            ->exists();
    }

    public function abrirControlCajaCliente(int $empresaId, int $userId, array $data): ControlCaja
    {
        $ahora = now(self::BOGOTA_TZ);
        $hora = (string) ($data['hora_apertura'] ?? $ahora->format('H:i:s'));
        $fechaApertura = $data['fecha_apertura'] ?? $ahora;

        $control = ControlCaja::create([
            'empresa_id' => $empresaId,
            'sucursal_id' => (int) $data['sucursal_id'],
            'caja_id' => (int) $data['caja_id'],
            'usuario_apertura_id' => (int) ($data['usuario_apertura_id'] ?? $userId),
            'usuario_cierre_id' => (int) ($data['usuario_apertura_id'] ?? $userId),
            'fecha_apertura' => $fechaApertura,
            'fecha_cierre' => $fechaApertura,
            'hora_apertura' => $hora,
            'hora_cierre' => $hora,
            'monto_apertura' => (float) ($data['monto_apertura'] ?? 0),
            'monto_cierre' => 0,
            'efectivo_sistema' => 0,
            'efectivo_contado' => 0,
            'diferencia' => 0,
            'observaciones_apertura' => $data['observaciones_apertura'] ?? null,
            'observaciones_cierre' => null,
            'estado' => 'Abierta',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return $control->fresh($this->detailRelations());
    }

    public function cerrarControlCajaCliente(
        int $controlCajaId,
        int $empresaId,
        int $userId,
        array $data,
        bool $canManageAll
    ): ControlCaja {
        $query = ControlCaja::query()
            ->where('id', $controlCajaId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'Abierta');

        if (!$canManageAll) {
            $query->where('usuario_apertura_id', $userId);
        }

        $control = $query->first();

        if (!$control) {
            throw new ModelNotFoundException('No se encontró una caja abierta para cerrar.');
        }

        $control->update([
            'usuario_cierre_id' => (int) ($data['usuario_cierre_id'] ?? $userId),
            'fecha_cierre' => $data['fecha_cierre'] ?? now(self::BOGOTA_TZ),
            'hora_cierre' => (string) ($data['hora_cierre'] ?? now(self::BOGOTA_TZ)->format('H:i:s')),
            'monto_cierre' => (float) ($data['monto_cierre'] ?? 0),
            'efectivo_sistema' => (float) ($data['efectivo_sistema'] ?? 0),
            'efectivo_contado' => (float) ($data['efectivo_contado'] ?? 0),
            'diferencia' => (float) ($data['diferencia'] ?? 0),
            'observaciones_cierre' => $data['observaciones_cierre'] ?? null,
            'estado' => 'Cerrada',
            'updated_by' => $userId,
        ]);

        return $control->fresh($this->detailRelations());
    }

    public function anularControlCajaCliente(
        int $controlCajaId,
        int $empresaId,
        int $userId,
        array $data,
        bool $canManageAll
    ): ControlCaja {
        $query = ControlCaja::query()
            ->where('id', $controlCajaId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'Abierta');

        if (!$canManageAll) {
            $query->where('usuario_apertura_id', $userId);
        }

        $control = $query->first();

        if (!$control) {
            throw new ModelNotFoundException('No se encontró una caja abierta para anular.');
        }

        $ahoraBogota = now(self::BOGOTA_TZ);

        $control->update([
            'usuario_cierre_id' => $userId,
            'fecha_cierre' => $ahoraBogota,
            'hora_cierre' => $ahoraBogota->format('H:i:s'),
            'observaciones_cierre' => $data['observaciones_cierre'],
            'estado' => 'Anulada',
            'updated_by' => $userId,
        ]);

        return $control->fresh($this->detailRelations());
    }
}
