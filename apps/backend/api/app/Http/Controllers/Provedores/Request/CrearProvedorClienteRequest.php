<?php

namespace App\Http\Controllers\Provedores\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearProvedorClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $provedorId = (int) ($this->route('provedorId') ?? 0);

        $numeroDocumentoRule = Rule::unique('proveedores', 'numero_documento')
            ->where(fn ($query) => $query->where('empresa_id', $empresaId));

        if ($provedorId > 0) {
            $numeroDocumentoRule->ignore($provedorId);
        }

        return [
            'tipo_documento_id' => 'required|integer|exists:tipos_documentos,id',
            'numero_documento' => ['required', 'string', 'max:255', $numeroDocumentoRule],
            'codigo_verificacion' => 'nullable|string|max:255',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'sitio_web' => 'nullable|string|max:255',
            'pais_id' => 'required|integer|exists:paises,id',
            'departamento_id' => 'required|integer|exists:departamentos,id',
            'ciudad_id' => 'required|integer|exists:ciudades,id',
            'codigo_postal' => 'nullable|string|max:30',
            'cupo_credito' => 'nullable|numeric|min:0',
            'estado_id' => 'nullable|integer|exists:estados,id',
            'dias_credito' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ];
    }
}
