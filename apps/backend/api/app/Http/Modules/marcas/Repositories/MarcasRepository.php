<?php

namespace App\Http\Modules\Marcas\Repositories;

use App\Models\Marcas\Marca;

class MarcasRepository
{
    public function listarMarcas()
    {
        return Marca::all();
    }
}
