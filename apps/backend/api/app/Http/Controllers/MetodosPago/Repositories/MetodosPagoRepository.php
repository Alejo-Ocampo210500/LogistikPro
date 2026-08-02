<?php

namespace App\Http\Controllers\MetodosPago\Repositories;

use App\Models\Pagos\MetodoPago;

class MetodosPagoRepository
{
    public function __construct(protected MetodoPago $MetodoPagoModel) {}

    public function listarMetodosPago()
    {
        return $this->MetodoPagoModel->select('id', 'nombre', 'descripcion')
            ->where('activo', true)
            ->orderBy('id', 'Asc')
            ->get();
    }
}
