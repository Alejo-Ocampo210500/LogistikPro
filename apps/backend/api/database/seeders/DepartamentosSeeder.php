<?php

namespace Database\Seeders;

use App\Models\Departamentos\Departamento;
use App\Models\Paises\Pais;
use Illuminate\Database\Seeder;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colombia = Pais::where('codigo_iso', 'CO')->first();

        if (! $colombia) {
            return;
        }

        $departamentos = [
            ['nombre' => 'Amazonas', 'codigo' => '91'],
            ['nombre' => 'Antioquia', 'codigo' => '05'],
            ['nombre' => 'Arauca', 'codigo' => '81'],
            ['nombre' => 'Atlantico', 'codigo' => '08'],
            ['nombre' => 'Bogota, D.C.', 'codigo' => '11'],
            ['nombre' => 'Bolivar', 'codigo' => '13'],
            ['nombre' => 'Boyaca', 'codigo' => '15'],
            ['nombre' => 'Caldas', 'codigo' => '17'],
            ['nombre' => 'Caqueta', 'codigo' => '18'],
            ['nombre' => 'Casanare', 'codigo' => '85'],
            ['nombre' => 'Cauca', 'codigo' => '19'],
            ['nombre' => 'Cesar', 'codigo' => '20'],
            ['nombre' => 'Choco', 'codigo' => '27'],
            ['nombre' => 'Cordoba', 'codigo' => '23'],
            ['nombre' => 'Cundinamarca', 'codigo' => '25'],
            ['nombre' => 'Guainia', 'codigo' => '94'],
            ['nombre' => 'Guaviare', 'codigo' => '95'],
            ['nombre' => 'Huila', 'codigo' => '41'],
            ['nombre' => 'La Guajira', 'codigo' => '44'],
            ['nombre' => 'Magdalena', 'codigo' => '47'],
            ['nombre' => 'Meta', 'codigo' => '50'],
            ['nombre' => 'Narino', 'codigo' => '52'],
            ['nombre' => 'Norte de Santander', 'codigo' => '54'],
            ['nombre' => 'Putumayo', 'codigo' => '86'],
            ['nombre' => 'Quindio', 'codigo' => '63'],
            ['nombre' => 'Risaralda', 'codigo' => '66'],
            ['nombre' => 'San Andres y Providencia', 'codigo' => '88'],
            ['nombre' => 'Santander', 'codigo' => '68'],
            ['nombre' => 'Sucre', 'codigo' => '70'],
            ['nombre' => 'Tolima', 'codigo' => '73'],
            ['nombre' => 'Valle del Cauca', 'codigo' => '76'],
            ['nombre' => 'Vaupes', 'codigo' => '97'],
            ['nombre' => 'Vichada', 'codigo' => '99'],
        ];

        foreach ($departamentos as $departamentoData) {
            Departamento::updateOrCreate(
                [
                    'nombre' => $departamentoData['nombre'],
                    'pais_id' => $colombia->id,
                ],
                [
                    'codigo' => $departamentoData['codigo'],
                    'estado_id' => 1,
                    'creado_por' => 1,
                ]
            );
        }
    }
}
