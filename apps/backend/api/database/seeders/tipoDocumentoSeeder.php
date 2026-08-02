<?php

namespace Database\Seeders;

use App\Models\TiposDocumento\tipoDocumento as TipoDocumentoModel;
use Illuminate\Database\Seeder;

class tipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDocumento = [
            [
                'nombre' => 'Cedula de ciudadania',
                'abreviatura' => 'CC',
                'descripcion' => 'Documento de identificacion para ciudadanos colombianos.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
            [
                'nombre' => 'Cedula de extranjeria',
                'abreviatura' => 'CE',
                'descripcion' => 'Documento de identificacion para extranjeros residentes.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
            [
                'nombre' => 'Tarjeta de identidad',
                'abreviatura' => 'TI',
                'descripcion' => 'Documento de identificacion para menores de edad.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
            [
                'nombre' => 'Pasaporte',
                'abreviatura' => 'PA',
                'descripcion' => 'Documento de viaje e identificacion internacional.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
            [
                'nombre' => 'Registro civil',
                'abreviatura' => 'RC',
                'descripcion' => 'Documento de registro civil de nacimiento.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
            [
                'nombre' => 'NIT',
                'abreviatura' => 'NIT',
                'descripcion' => 'Numero de Identificacion Tributaria.',
                'estado_id' => 1,
                'creado_por' => 1,
                'actualizado_por' => 1,
            ],
        ];

        foreach ($tiposDocumento as $tipoDocumentoData) {
            TipoDocumentoModel::updateOrCreate(
                [
                    'abreviatura' => $tipoDocumentoData['abreviatura'],
                ],
                $tipoDocumentoData
            );
        }
    }
}
