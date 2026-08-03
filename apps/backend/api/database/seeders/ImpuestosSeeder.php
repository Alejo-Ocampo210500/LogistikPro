<?php

namespace Database\Seeders;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Impuesto\impuesto as Impuesto;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImpuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresaId = Empresa::query()->value('id');
        $estadoActivoId = Estado::where('nombre', 'Activo')->value('id');
        $usuarioId = User::query()->value('id');

        if (! $empresaId) {
            return;
        }

        $impuestos = [
            [
                'nombre' => 'IVA 19%',
                'codigo' => 'IVA19',
                'porcentaje' => 19,
                'descripcion' => 'Impuesto sobre las ventas del 19%',
            ],
            [
                'nombre' => 'IVA 5%',
                'codigo' => 'IVA5',
                'porcentaje' => 5,
                'descripcion' => 'Impuesto sobre las ventas del 5%',
            ],
            [
                'nombre' => 'Exento',
                'codigo' => 'EXENTO',
                'porcentaje' => 0,
                'descripcion' => 'Producto exento de IVA',
            ],
            [
                'nombre' => 'Excluido',
                'codigo' => 'EXCLUIDO',
                'porcentaje' => 0,
                'descripcion' => 'Producto excluido de IVA',
            ],
        ];

        foreach ($impuestos as $impuestoData) {
            Impuesto::updateOrCreate(
                [
                    'empresa_id' => $empresaId,
                    'codigo' => $impuestoData['codigo'],
                ],
                [
                    'nombre' => $impuestoData['nombre'],
                    'porcentaje' => $impuestoData['porcentaje'],
                    'descripcion' => $impuestoData['descripcion'],
                    'estado_id' => $estadoActivoId,
                    'creado_por' => $usuarioId,
                    'actualizado_por' => $usuarioId,
                ]
            );
        }
    }
}
