<?php

namespace App\Http\Controllers\Ciudades\Repositories;

use App\Models\Ciudades\ciudad;

class CiudadesRepository
{
    public function obtenerCiudadesCliente()
    {
        return ciudad::where('estado_id', 1)->get();
    }
}
