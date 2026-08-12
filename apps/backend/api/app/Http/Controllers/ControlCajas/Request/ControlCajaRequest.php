<?php

namespace App\Http\Controllers\ControlCajas\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ControlCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()?->getActionMethod();

        if ($action === 'abrirControlCajaCliente') {
            return [
                'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
                'caja_id' => ['required', 'integer', 'exists:cajas,id'],
                'usuario_apertura_id' => ['nullable', 'integer', 'exists:users,id'],
                'monto_apertura' => ['nullable', 'numeric', 'min:0'],
                'observaciones_apertura' => ['nullable', 'string', 'max:255'],
                'fecha_apertura' => ['nullable', 'date'],
                'hora_apertura' => ['nullable', 'date_format:H:i:s'],
            ];
        }

        if ($action === 'cerrarControlCajaCliente') {
            return [
                'usuario_cierre_id' => ['nullable', 'integer', 'exists:users,id'],
                'monto_cierre' => ['nullable', 'numeric', 'min:0'],
                'efectivo_sistema' => ['nullable', 'numeric', 'min:0'],
                'efectivo_contado' => ['nullable', 'numeric', 'min:0'],
                'diferencia' => ['nullable', 'numeric'],
                'observaciones_cierre' => ['nullable', 'string', 'max:255'],
                'fecha_cierre' => ['nullable', 'date'],
                'hora_cierre' => ['nullable', 'date_format:H:i:s'],
            ];
        }

        if ($action === 'anularControlCajaCliente') {
            return [
                'estado' => ['nullable', Rule::in(['Anulada'])],
                'observaciones_cierre' => ['required', 'string', 'min:3', 'max:255'],
            ];
        }

        return [];
    }
}
