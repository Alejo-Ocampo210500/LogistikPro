<?php

namespace App\Http\Controllers\TipoDocumento;

use App\Http\Controllers\TipoDocumento\Repositories\TipoDocumentoRepository;
use App\Http\Controllers\Controller;

class PanelTipoDocumentoClienteController extends Controller
{
    public function __construct(protected TipoDocumentoRepository $TipoDocumentoRepository) {}

    public function obtenerTipoDocumentosCliente()
    {
        try {
            $listarTipoDocumentos = $this->TipoDocumentoRepository->obtenerTipoDocumentosCliente();
            return response()->json([$listarTipoDocumentos], 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al obtener los tipos de documento'
            ], 400);
        }
    }
}
