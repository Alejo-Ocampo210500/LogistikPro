<?php

namespace App\Http\Controllers\Estados\Services;

use App\Models\Estados\Estado;

class EstadosService
{
    public function obtenerEstados()
    {
        return Estado::orderBy('nombre', 'desc')->get();
    }
}
