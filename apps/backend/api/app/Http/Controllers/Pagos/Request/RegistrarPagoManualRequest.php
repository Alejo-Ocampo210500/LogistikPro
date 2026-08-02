<?php

namespace App\Http\Controllers\Pagos\Request;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarPagoManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer'],
            'plan_id' => ['required', 'integer'],
            'metodo_pago_id' => ['required', 'integer'],
            'valor' => ['required', 'numeric'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
            'referencia' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
