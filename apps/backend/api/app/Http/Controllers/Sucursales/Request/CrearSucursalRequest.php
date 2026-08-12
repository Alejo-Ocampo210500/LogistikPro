<?php

namespace App\Http\Controllers\Sucursales\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $sucursalId = (int) ($this->route('sucursalId') ?? 0);

        $codigoRule = Rule::unique('sucursales', 'codigo')
            ->where(fn ($query) => $query->where('empresa_id', $empresaId));

        $nitRule = Rule::unique('sucursales', 'nit')
            ->where(fn ($query) => $query->where('empresa_id', $empresaId));

        $emailRule = Rule::unique('sucursales', 'email')
            ->where(fn ($query) => $query->where('empresa_id', $empresaId));

        if ($sucursalId > 0) {
            $codigoRule->ignore($sucursalId);
            $nitRule->ignore($sucursalId);
            $emailRule->ignore($sucursalId);
        }

        return [
            'codigo' => ['required', 'string', 'max:255', $codigoRule],
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:255', $nitRule],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'ciudad_id' => ['required', 'integer', Rule::exists('ciudades', 'id')],
            'departamento_id' => ['required', 'integer', Rule::exists('departamentos', 'id')],
            'pais_id' => ['required', 'integer', Rule::exists('paises', 'id')],
            'responsable' => ['required', 'string', 'max:255'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
