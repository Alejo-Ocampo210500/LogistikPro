<?php

namespace App\Http\Modules\Empresas\Repositories;

use App\Models\Empresas\Empresa;

class EmpresaRepository
{
    /**
     * Funcion del repositorio que recibe la peticion para listar
     * las empresas y devolver la informacion de las empresas
     *
     * @author Alejandro Ocampos
     */
    public function listarEmpresasPorId()
    {
        return Empresa::where(
            'estado_id', 1)
        ->get();
    }

}
