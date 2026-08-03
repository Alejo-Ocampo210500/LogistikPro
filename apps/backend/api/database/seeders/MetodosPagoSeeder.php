<?php

namespace Database\Seeders;

use App\Models\Pagos\MetodoPago;
use Illuminate\Database\Seeder;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metodos = [
            [
                'nombre' => 'Nequi',
                'descripcion' => 'Pago por Nequi',
                'activo' => true,
            ],
            [
                'nombre' => 'Daviplata',
                'descripcion' => 'Pago por Daviplata',
                'activo' => true,
            ],
            [
                'nombre' => 'Bancolombia',
                'descripcion' => 'Pago por Bancolombia',
                'activo' => true,
            ],
            [
                'nombre' => 'PSE',
                'descripcion' => 'Pago por PSE',
                'activo' => true,
            ],
            [
                'nombre' => 'Tarjeta de Crédito',
                'descripcion' => 'Pago por Tarjeta de Crédito',
                'activo' => true,
            ],
            [
                'nombre' => 'Tarjeta de Débito',
                'descripcion' => 'Pago por Tarjeta de Débito',
                'activo' => true,
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'descripcion' => 'Pago por Transferencia Bancaria',
                'activo' => true,
            ],
            [
                'nombre' => 'PayPal',
                'descripcion' => 'Pago por PayPal',
                'activo' => true,
            ],
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago en efectivo',
                'activo' => true,
            ],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::updateOrCreate(
                ['nombre' => $metodo['nombre']],
                $metodo
            );
        }
    }
}
