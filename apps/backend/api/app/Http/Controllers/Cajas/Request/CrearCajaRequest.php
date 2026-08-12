<?php

namespace App\Http\Controllers\Cajas\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $cajaId = (int) ($this->route('cajaId') ?? 0);

        $codigoRule = Rule::unique('cajas', 'codigo')
            ->where(fn ($query) => $query->where('empresa_id', $empresaId));

        if ($cajaId > 0) {
            $codigoRule->ignore($cajaId);
        }

        return [
            'sucursal_id' => [
                'required',
                'integer',
                Rule::exists('sucursales', 'id')->where(fn ($query) => $query->where('empresa_id', $empresaId)),
            ],
            'codigo' => ['nullable', 'string', 'max:255', $codigoRule],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'impresora' => ['nullable', 'string', 'max:255'],
            'estado_id' => ['nullable', 'integer', Rule::exists('estados', 'id')],
        ];
    }
}
