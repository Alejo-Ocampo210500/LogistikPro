<?php

namespace App\Http\Controllers\Planes\Request;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'duracion_meses' => 'required|integer|min:1',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'estado_id' => 'required|integer|exists:estados,id'
        ];
    }
}
