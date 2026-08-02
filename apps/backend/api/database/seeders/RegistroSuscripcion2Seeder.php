<?php

namespace Database\Seeders;

use App\Models\Suscripcion;
use Illuminate\Database\Seeder;

class RegistroSuscripcion2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Suscripcion::insert([
            'empresa_id' => 8,
            'plan_id' => 7,
            'estado_id' => 4,
            'fecha_inicio' => '2026-07-17',
            'fecha_final' => '2026-08-17',
            'fecha_vencimiento' => '2026-08-17',
            'usuarios_contratados' => '1/5',
            'valor_pagado' => 39000,
            'renovacion' => 'Manual'
        ]);
    }
}
