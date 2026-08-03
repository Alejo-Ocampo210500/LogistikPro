<?php

namespace Database\Seeders;

use App\Models\Estados\Estado;
use App\Models\Paises\Pais;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadoActivoId = Estado::where('nombre', 'Activo')->value('id');
        $usuarioId = User::query()->value('id');

        $paises = [
            ['nombre' => 'Colombia', 'codigo_iso' => 'CO', 'estado_id' => $estadoActivoId, 'creado_por' => $usuarioId],
        ];

        foreach ($paises as $paisData) {
            Pais::updateOrCreate(
                ['codigo_iso' => $paisData['codigo_iso']],
                $paisData
            );
        }
    }
}
