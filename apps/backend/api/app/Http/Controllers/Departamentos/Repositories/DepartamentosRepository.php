<?php

namespace App\Http\Controllers\Departamentos\Repositories;

use App\Models\Departamentos\Departamento;

class DepartamentosRepository
{
    public function obtenerDepartamentosCliente()
    {
        return Departamento::where('estado_id', 1)->get();
    }
}
