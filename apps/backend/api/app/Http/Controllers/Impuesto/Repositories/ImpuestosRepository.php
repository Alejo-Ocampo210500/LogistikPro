<?php

namespace App\Http\Controllers\Impuesto\Repositories;

use App\Models\Impuesto\impuesto;

class ImpuestosRepository
{
    public function obtenerImpuestosCliente()
    {
        return impuesto::query()
            ->where('estado_id', 1)
            ->orderByDesc('id')
            ->get();
    }
}
