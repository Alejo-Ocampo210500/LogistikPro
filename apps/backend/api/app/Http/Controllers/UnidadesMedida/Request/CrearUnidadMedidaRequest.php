<?php

namespace App\Http\Controllers\UnidadesMedida\Request;

use App\Models\UnidadMedida\UnidadMedida;
use Illuminate\Foundation\Http\FormRequest;

class CrearUnidadMedidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $unidadId = (int) ($this->route('unidadId') ?? 0);
        $ignoreId = $unidadId > 0 ? $unidadId : 'NULL';

        return [
            'nombre' => 'required|string|max:255|unique:unidades_medida,nombre,' . $ignoreId . ',id,empresa_id,' . $empresaId,
            'descripcion' => 'nullable|string|max:1000',
            'abreviatura' => 'nullable|string|max:10',
            'estado_id' => 'nullable|integer',
        ];
    }
}
