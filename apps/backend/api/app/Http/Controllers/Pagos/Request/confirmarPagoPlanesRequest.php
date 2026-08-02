<?php

namespace App\Http\Controllers\Pagos\Request;

use Illuminate\Foundation\Http\FormRequest;

class confirmarPagoPlanesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pago_id' => ['required', 'integer'],
            'estado_pago_id' => ['required', 'integer'],
            'estado_id' => ['required', 'integer'],
            'fecha_pago' => ['required', 'date'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
        ];
    }
}
