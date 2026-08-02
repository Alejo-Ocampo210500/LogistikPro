<?php

namespace Database\Seeders;

use App\Models\Empresas\Empresa;
use App\Models\Impuesto\impuesto as Impuesto;
use Illuminate\Database\Seeder;

class ImpuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresaId = Empresa::query()->value('id');

        if (!$empresaId) {
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
            Impuesto::create([
                'empresa_id' => $empresaId,
                'nombre' => $impuestoData['nombre'],
                'codigo' => $impuestoData['codigo'],
                'porcentaje' => $impuestoData['porcentaje'],
                'descripcion' => $impuestoData['descripcion'],
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ]);
        }
    }
}
