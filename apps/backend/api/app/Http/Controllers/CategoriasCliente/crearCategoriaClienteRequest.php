<?php

namespace App\Http\Controllers\CategoriasCliente;

use Illuminate\Foundation\Http\FormRequest;

class CrearCategoriaClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);

        return [
            'nombre' => 'required|string|max:255|unique:categorias,nombre,NULL,id,empresa_id,' . $empresaId,
            'descripcion' => 'nullable|string|max:1000',
            'estado_id' => 'nullable|integer',
        ];
    }
}
