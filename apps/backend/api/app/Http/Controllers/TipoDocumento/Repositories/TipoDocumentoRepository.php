<?php

namespace App\Http\Controllers\TipoDocumento\Repositories;

use App\Models\TiposDocumento\tipoDocumento;

class TipoDocumentoRepository
{
    public function obtenerTipoDocumentosCliente()
    {
        return tipoDocumento::where('estado_id', 1)->get();
    }
}
