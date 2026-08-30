<?php

namespace App\Http\Modules\Empresas\Services;

use App\Models\Empresas\Empresa;

class EmpresaService
{
    /**
     * Funcion del servicio que recibe la peticion para crear una empresa y
     * devolver la informacion de la empresa creada
     *
     * @param array $data
     *
     * @author Alejandro Ocampos
     */
    public function crearEmpresa(array $data)
    {
        return Empresa::create($data);
    }
}
