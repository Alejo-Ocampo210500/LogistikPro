<?php

namespace App\Http\Controllers\Mantenimiento\Request;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarUsuarioGlobalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:3',
            'apellido' => 'required|string|min:3',
            'telefono' => 'required|string|min:9',
            'email' => 'required|email',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'estado_id' => 'required|exists:estados,id',
        ];
    }
}
