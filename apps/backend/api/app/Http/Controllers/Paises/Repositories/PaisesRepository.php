<?php

namespace App\Http\Controllers\Paises\Repositories;

use App\Models\Paises\Pais;

class PaisesRepository
{
    public function obtenerPaisesCliente()
    {
        return Pais::where('estado_id', 1)->get();
    }
}
