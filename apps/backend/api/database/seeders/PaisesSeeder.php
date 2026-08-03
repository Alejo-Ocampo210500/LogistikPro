<?php

namespace Database\Seeders;

use App\Models\Paises\Pais;
use Illuminate\Database\Seeder;

class PaisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paises = [
            ['nombre' => 'Colombia', 'codigo_iso' => 'CO', 'estado_id' => 1, 'creado_por' => 1],
        ];

        foreach ($paises as $paisData) {
            Pais::updateOrCreate(
                ['codigo_iso' => $paisData['codigo_iso']],
                $paisData
            );
        }
    }
}
