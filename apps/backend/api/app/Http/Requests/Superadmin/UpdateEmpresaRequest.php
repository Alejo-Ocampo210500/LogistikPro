<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa')?->id;

        return [
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'razon_social' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:30', Rule::unique('empresas', 'nit')->ignore($empresaId)],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'ciudad_id' => ['required', 'integer', 'exists:ciudades,id'],
            'logo' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'integer', 'exists:planes,id'],
            'estado_id' => ['required', 'integer', 'exists:estados,id'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe una empresa registrada con este NIT.',
            'departamento_id.required' => 'Debes seleccionar un departamento.',
            'departamento_id.integer' => 'El departamento seleccionado no es válido.',
            'departamento_id.exists' => 'El departamento seleccionado no es válido.',
            'ciudad_id.required' => 'Debes seleccionar una ciudad.',
            'ciudad_id.integer' => 'La ciudad seleccionada no es válida.',
            'ciudad_id.exists' => 'La ciudad seleccionada no es válida.',
            'plan_id.integer' => 'El plan seleccionado no es válido.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'estado_id.required' => 'El estado de la empresa es obligatorio.',
            'estado_id.exists' => 'El estado seleccionado no es válido.',
        ];
    }
}
