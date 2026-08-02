<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_comercial' => ['required', 'string', 'max:255'],
            'razon_social' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:30', 'unique:empresas,nit'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'ciudad_id' => ['required', 'integer', 'exists:ciudades,id'],
            'logo' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'integer', 'exists:planes,id'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'admin_nombre' => ['required', 'string', 'max:255'],
            'admin_apellido' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_telefono' => ['nullable', 'string', 'max:30'],
            'admin_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
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
            'admin_nombre.required' => 'El nombre del administrador es obligatorio.',
            'admin_apellido.required' => 'El apellido del administrador es obligatorio.',
            'admin_email.required' => 'El correo del administrador es obligatorio.',
            'admin_email.email' => 'El correo del administrador debe ser válido.',
            'admin_email.unique' => 'Ya existe un usuario con ese correo.',
            'admin_password.required' => 'La contraseña del administrador es obligatoria.',
            'admin_password.min' => 'La contraseña del administrador debe tener al menos 8 caracteres.',
            'admin_password.regex' => 'La contraseña del administrador debe contener al menos una letra minúscula, una letra mayúscula y un número.',
        ];
    }
}
