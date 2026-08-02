<?php

namespace Database\Seeders;

use App\Models\Pagos\EstadoPago;
use Illuminate\Database\Seeder;

class EstadosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            [
                'nombre'        => 'Pendiente',
                'descripcion'   => 'Pago pendiente',
                'activo'        => true,
            ],
            [
                'nombre'        => 'Aprobado',
                'descripcion'   => 'Pago aprobado',
                'activo'        => true,
            ],
            [
                'nombre'        => 'Rechazado',
                'descripcion'   => 'Pago rechazado',
                'activo'        => true,
            ],
            [
                'nombre'        => 'Cancelado',
                'descripcion'   => 'Pago cancelado',
                'activo'        => true,
            ],
        ];

        foreach ($estados as $estado) {
            EstadoPago::create($estado);
        }
    }
}
