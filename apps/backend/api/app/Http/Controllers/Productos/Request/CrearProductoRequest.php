<?php

namespace App\Http\Controllers\Productos\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = (int) ($this->user()?->empresa_id ?? 0);
        $productoId = (int) ($this->route('productoId') ?? 0);

        return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo')
                    ->where(fn ($query) => $query->where('empresa_id', $empresaId))
                    ->ignore($productoId > 0 ? $productoId : null),
            ],
            'codigo_barras' => 'nullable|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'categoria_id' => [
                'required',
                'integer',
                Rule::exists('categorias', 'id')->where(fn ($query) => $query->where('empresa_id', $empresaId)),
            ],
            'marca_id' => [
                'nullable',
                'integer',
                Rule::exists('marcas', 'id')->where(fn ($query) => $query->where('empresa_id', $empresaId)),
            ],
            'unidad_medida_id' => [
                'required',
                'integer',
                Rule::exists('unidades_medida', 'id')->where(fn ($query) => $query->where('empresa_id', $empresaId)),
            ],
            'impuesto_id' => [
                'nullable',
                'integer',
                Rule::exists('impuestos', 'id'),
            ],
            'costo' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'maneja_inventario' => 'nullable|boolean',
            'permite_descuento' => 'nullable|boolean',
            'es_servicio' => 'nullable|boolean',
            'venta_libre' => 'nullable|boolean',
            'estado_id' => 'required|integer|exists:estados,id',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'El codigo interno ya esta en uso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizarNullableId = static function ($value) {
            if ($value === null || $value === '' || $value === false) {
                return null;
            }

            $cast = (int) $value;
            return $cast > 0 ? $cast : null;
        };

        $this->merge([
            'marca_id' => $normalizarNullableId($this->input('marca_id')),
            'impuesto_id' => $normalizarNullableId($this->input('impuesto_id')),
            'stock_maximo' => $normalizarNullableId($this->input('stock_maximo')),
        ]);
    }
}
