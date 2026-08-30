<?php

namespace App\Http\Modules\Empresas\Request;

use Illuminate\Foundation\Http\FormRequest;

class CrearEmpresaRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'numero_documento' => ['required', 'string', 'unique:empresas,numero_documento'],
			'digito_verificacion' => ['nullable', 'string'],
			'nombre_comercial' => ['required', 'string'],
			'logo' => ['nullable', 'string'],
			'telefono' => ['nullable', 'string'],
			'email' => ['required', 'email'],
			'sitio_web' => ['nullable', 'string'],
			'direccion' => ['nullable', 'string'],
			'codigo_postal' => ['nullable', 'string'],
			'created_by' => ['required', 'integer', 'exists:users,id'],
			'updated_by' => ['required', 'integer', 'exists:users,id'],
		];
	}
}
