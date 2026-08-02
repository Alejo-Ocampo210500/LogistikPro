<?php

namespace Database\Seeders;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Planes\Plan;
use App\Models\Suscripcion;
use Illuminate\Database\Seeder;

class RegistroSuscripcion2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = Empresa::where('nit', '9000000001')->firstOrFail();
        $plan = Plan::where('nombre', 'basico')->firstOrFail();
        $estado = Estado::where('nombre', 'Activo')->firstOrFail();

        Suscripcion::updateOrCreate([
            'empresa_id' => $empresa->id,
            'plan_id' => $plan->id,
        ], [
            'estado_id' => $estado->id,
            'fecha_inicio' => '2026-07-17',
            'fecha_final' => '2026-08-17',
            'fecha_vencimiento' => '2026-08-17',
            'usuarios_contratados' => '1/5',
            'valor_pagado' => 39000,
            'renovacion' => false,
        ]);
    }
}
