<?php

namespace App\Http\Controllers\Clientes\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CrearClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = (int) ($this->route('clienteId') ?? 0);

        return [
            'tipo_persona' => ['required', Rule::in(['juridica', 'natural'])],
            'tipo_documento_id' => ['required', 'integer', 'exists:tipos_documentos,id'],
            'numero_documento' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clientes', 'numero_documento')->ignore($clienteId),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clientes', 'email')->ignore($clienteId),
            ],
            'celular' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'pais_id' => ['required', 'integer', 'exists:paises,id'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'ciudad_id' => ['required', 'integer', 'exists:ciudades,id'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'genero' => ['nullable', Rule::in(['masculino', 'femenino'])],
            'limite_credito' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'saldo_credito' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'dias_credito' => ['nullable', 'integer', 'min:0'],
            'estado_id' => ['required', 'integer', 'exists:estados,id'],

            // Valores de contexto controlados por backend.
            'empresa_id' => ['prohibited'],
            'creado_por' => ['prohibited'],
            'actualizado_por' => ['prohibited'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $limiteCredito = (float) ($this->input('limite_credito') ?? 0);
            $saldoCredito = (float) ($this->input('saldo_credito') ?? 0);

            if ($limiteCredito > $saldoCredito) {
                $validator->errors()->add('limite_credito', 'El limite de credito no puede ser mayor que el saldo credito.');
            }
        });
    }
}
