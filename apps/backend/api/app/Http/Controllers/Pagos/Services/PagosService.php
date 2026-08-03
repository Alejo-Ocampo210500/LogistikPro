<?php

namespace App\Http\Controllers\Pagos\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PagosService
{
    private ?bool $pagosTieneSnapshot = null;

    private function obtenerEstadoActivoId(): ?int
    {
        return DB::table('estados')
            ->whereIn('nombre', ['Activo', 'ACTIVO', 'activo'])
            ->value('id');
    }

    private function obtenerPagoDetalladoPorId(int $pagoId): ?object
    {
        $query = DB::table('pagos')
            ->join('suscripciones', 'pagos.suscripcion_id', '=', 'suscripciones.id')
            ->join('empresas', 'suscripciones.empresa_id', '=', 'empresas.id')
            ->join('planes as planes_actuales', 'suscripciones.plan_id', '=', 'planes_actuales.id')
            ->join('metodos_pago', 'pagos.metodo_pago_id', '=', 'metodos_pago.id')
            ->join('estados_pago', 'pagos.estado_pago_id', '=', 'estados_pago.id')
            ->where('pagos.id', $pagoId)
            ->select(
                'pagos.*',
                'suscripciones.empresa_id as empresa_id',
                'suscripciones.plan_id as plan_id',
                'empresas.nombre_comercial as empresa_nombre',
                'planes_actuales.nombre as plan_nombre',
                'suscripciones.fecha_inicio',
                'suscripciones.fecha_vencimiento',
                'metodos_pago.nombre as metodo_pago',
                'estados_pago.nombre as estado_pago'
            );

        if ($this->pagosTieneSnapshot()) {
            $query->leftJoin('planes as planes_pago', 'pagos.plan_id', '=', 'planes_pago.id')
                ->addSelect(DB::raw('COALESCE(pagos.plan_id, suscripciones.plan_id) as plan_id'))
                ->addSelect(DB::raw('COALESCE(planes_pago.nombre, planes_actuales.nombre) as plan_nombre'));
        }

        return $query->first();
    }

    public function obtenerPagos()
    {
        $query = DB::table('pagos')
            ->join('suscripciones', 'pagos.suscripcion_id', '=', 'suscripciones.id')
            ->join('empresas', 'suscripciones.empresa_id', '=', 'empresas.id')
            ->join('planes as planes_actuales', 'suscripciones.plan_id', '=', 'planes_actuales.id')
            ->join('metodos_pago', 'pagos.metodo_pago_id', '=', 'metodos_pago.id')
            ->join('estados_pago', 'pagos.estado_pago_id', '=', 'estados_pago.id')
            ->select(
                'pagos.*',
                'suscripciones.empresa_id as empresa_id',
                'suscripciones.plan_id as plan_id',
                'empresas.nombre_comercial as empresa_nombre',
                'planes_actuales.nombre as plan_nombre',
                'suscripciones.fecha_inicio',
                'suscripciones.fecha_vencimiento',
                'metodos_pago.nombre as metodo_pago',
                'estados_pago.nombre as estado_pago'
            );

        if ($this->pagosTieneSnapshot()) {
            $query->leftJoin('planes as planes_pago', 'pagos.plan_id', '=', 'planes_pago.id')
                ->addSelect(DB::raw('COALESCE(pagos.plan_id, suscripciones.plan_id) as plan_id'))
                ->addSelect(DB::raw('COALESCE(planes_pago.nombre, planes_actuales.nombre) as plan_nombre'));
        }

        return $query->orderByDesc('pagos.fecha_pago')->orderByDesc('pagos.id')->get();
    }

    public function listarPagosPlanEmpresa($empresa_id)
    {
        $query = DB::table('suscripciones')
            ->join('planes as planes_actuales', 'suscripciones.plan_id', '=', 'planes_actuales.id')
            ->where('suscripciones.empresa_id', $empresa_id)
            ->select(
                'suscripciones.id',
                'suscripciones.plan_id',
                'suscripciones.fecha_inicio',
                'suscripciones.fecha_vencimiento',
                'planes_actuales.nombre as plan_nombre'
            );

        $estadoActivoId = $this->obtenerEstadoActivoId();

        if ($estadoActivoId !== null) {
            $suscripcionActiva = (clone $query)
                ->where('suscripciones.estado_id', $estadoActivoId)
                ->orderByDesc('suscripciones.id')
                ->first();

            if ($suscripcionActiva) {
                return $suscripcionActiva;
            }
        }

        // Si no hay suscripción activa, devolver la última existente para clientes antiguos.
        return $query->orderByDesc('suscripciones.id')->first();
    }

    private function resolveTipoPago(?object $suscripcionActual, int $planId): string
    {
        if (!$suscripcionActual) {
            return 'Primer pago';
        }

        if ((int) $suscripcionActual->plan_id === (int) $planId) {
            return 'Renovación';
        }

        return 'Cambio de plan';
    }

    private function pagosTieneSnapshot(): bool
    {
        if ($this->pagosTieneSnapshot !== null) {
            return $this->pagosTieneSnapshot;
        }

        $this->pagosTieneSnapshot = Schema::hasColumn('pagos', 'plan_id') && Schema::hasColumn('pagos', 'tipo_pago');

        return $this->pagosTieneSnapshot;
    }

    private function obtenerSuscripcionActiva(int $empresaId): ?object
    {
        $query = DB::table('suscripciones')
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id');

        $estadoActivoId = $this->obtenerEstadoActivoId();

        if ($estadoActivoId !== null) {
            $query->where('estado_id', $estadoActivoId);
        } else {
            $query->where('estado_id', 4);
        }

        return $query->first();
    }

    private function obtenerUltimaSuscripcionEmpresa(int $empresaId): ?object
    {
        return DB::table('suscripciones')
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->first();
    }

    public function confirmarPagoPlanes($data)
    {
        $suscripcion = DB::transaction(function () use ($data) {
            $pago = DB::table('pagos')
                ->where('id', $data['pago_id'])
                ->first();

            $suscripcion = DB::table('suscripciones')
                ->where('id', $pago->suscripcion_id)
                ->first();

            $planId = $suscripcion->plan_id;
            $tipoPago = $this->resolveTipoPago($this->obtenerSuscripcionActiva((int) $suscripcion->empresa_id), (int) $planId);

            $updatePago = [
                'estado_pago_id' => $data['estado_pago_id'],
                'fecha_pago' => $data['fecha_pago'],
            ];

            if ($this->pagosTieneSnapshot()) {
                $updatePago['plan_id'] = $planId;
                $updatePago['tipo_pago'] = $tipoPago;
            }

            DB::table('pagos')->where('id', $pago->id)->update($updatePago);

            $estadoActivoId = DB::table('estados')
                ->where('nombre', 'Activo')
                ->value('id') ?? 1;

            DB::table('suscripciones')->where('id', $suscripcion->id)->update([
                'estado_id' => $estadoActivoId,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_vencimiento' => $data['fecha_vencimiento']
            ]);

            $plan = DB::table('planes')->where('id', $planId)->first();
            DB::table('empresas')
                ->where('id', $suscripcion->empresa_id)
                ->update([
                    'plan' => $plan ? $plan->nombre : '',
                    'plan_id' => $planId,
                    'fecha_vencimiento' => $data['fecha_vencimiento'],
                    'updated_at' => now(),
                ]);

            return $pago;
        });

        return [
            'message' => 'Pago confirmado exitosamente',
            'data' => $suscripcion
        ];
    }

    public function registrarPagoManual($data)
    {
        $pagoDetallado = DB::transaction(function () use ($data) {
            $empresaId = (int) $data['empresa_id'];
            $planId = (int) $data['plan_id'];
            $metodoPagoId = $data['metodo_pago_id'];
            $valor = $data['valor'];
            $fechaInicio = $data['fecha_inicio'];
            $fechaVencimiento = $data['fecha_vencimiento'];
            $referencia = $data['referencia'] ?? null;
            $observaciones = $data['observaciones'] ?? null;

            $estadoActivoId = DB::table('estados')
                ->where('nombre', 'Activo')
                ->value('id') ?? 1;

            $suscripcionActual = $this->obtenerSuscripcionActiva($empresaId);
            $suscripcionReferencia = $suscripcionActual ?: $this->obtenerUltimaSuscripcionEmpresa($empresaId);
            $tipoPago = $this->resolveTipoPago($suscripcionReferencia, $planId);

            if ($suscripcionActual) {
                DB::table('suscripciones')
                    ->where('id', $suscripcionActual->id)
                    ->update([
                        'plan_id' => $planId,
                        'estado_id' => $estadoActivoId,
                        'fecha_inicio' => $fechaInicio,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'fecha_final' => $fechaVencimiento,
                        'valor_pagado' => $valor,
                        'renovacion' => $tipoPago === 'Renovación',
                        'updated_at' => now(),
                    ]);
                $suscripcionId = $suscripcionActual->id;
            } else {
                $suscripcionId = DB::table('suscripciones')->insertGetId([
                    'empresa_id' => $empresaId,
                    'plan_id' => $planId,
                    'estado_id' => $estadoActivoId,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'fecha_final' => $fechaVencimiento,
                    'usuarios_contratados' => '1/5',
                    'valor_pagado' => $valor,
                    'renovacion' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $pagoInsert = [
                'suscripcion_id' => $suscripcionId,
                'metodo_pago_id' => $metodoPagoId,
                'estado_pago_id' => 2,
                'valor' => $valor,
                'fecha_pago' => now()->toDateString(),
                'referencia' => $referencia,
                'observaciones' => $observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->pagosTieneSnapshot()) {
                $pagoInsert['plan_id'] = $planId;
                $pagoInsert['tipo_pago'] = $tipoPago;
            }

            $pagoId = DB::table('pagos')->insertGetId($pagoInsert);

            $plan = DB::table('planes')->where('id', $planId)->first();
            DB::table('empresas')
                ->where('id', $empresaId)
                ->update([
                    'plan' => $plan ? $plan->nombre : '',
                    'plan_id' => $planId,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'updated_at' => now(),
                ]);

            return $this->obtenerPagoDetalladoPorId((int) $pagoId);
        });

        return [
            'message' => 'Pago manual registrado exitosamente',
            'data' => $pagoDetallado
        ];
    }
}
