<?php

namespace App\Http\Controllers\Marcas\Request;

use Illuminate\Foundation\Http\FormRequest;

class CrearMarcasClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $marcaId = (int) ($this->route('marcasId') ?? 0);
        $ignoreId = $marcaId > 0 ? $marcaId : 'NULL';

        return [
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $ignoreId . ',id,empresa_id,' . $empresaId,
            'descripcion' => 'nullable|string|max:1000',
            'logo' => 'nullable|string|max:255',
            'sitio_web' => 'nullable|string|max:255',
            'estado_id' => 'nullable|integer',
        ];
    }
}
