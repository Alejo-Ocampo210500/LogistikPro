<?php

namespace Database\Seeders;

use App\Models\Estados\Estado;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        Estado::insert([
            ['nombre' => 'Activo'],
            ['nombre' => 'inactivo'],
            ['nombre' => 'suspendida'],
            ['nombre' => 'En Revision'],
            ['nombre' => 'Pendinte de Pago'],
            ['nombre' => 'Vencida'],
            ['nombre' => 'Cancelada'],
            ['nombre' => 'Prueba Gratuita'],
            ['nombre' => 'Bloqueado'],
        ]);
    }
}