<?php

namespace App\Http\Modules\Marcas\Request;

use Illuminate\Foundation\Http\FormRequest;

class crearMarcaRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
			'codigo_marca' => ['nullable', 'string', 'max:255', 'unique:marcas,codigo_marca'],
			'nombre_marca' => ['required', 'string', 'max:255'],
			'descripcion_marca' => ['required', 'string', 'max:255'],
			'created_by' => ['required', 'integer', 'exists:users,id'],
			'updated_by' => ['required', 'integer', 'exists:users,id'],
		];
	}
}
