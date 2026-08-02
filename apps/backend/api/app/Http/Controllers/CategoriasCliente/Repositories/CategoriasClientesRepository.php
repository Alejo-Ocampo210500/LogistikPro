<?php

namespace App\Http\Controllers\CategoriasCliente\Repositories;

use App\Models\Categorias\categoria as CategoriaModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoriasClientesRepository
{
    /**
     * Funcion que obtiene las categorias del cliente autenticado según el ID de la empresa
     * proporcionado.
     *
     * @param int $empresaId
     *
     * @author Alejandro Ocampo
     */
    public function obtenerCategoriasCliente(int $empresaId): Collection
    {
        return CategoriaModel::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Funcion que obtiene una categoria del cliente autenticado según el ID de la categoria
     * y el ID de la empresa proporcionados.
     *
     * @param int $categoriaId
     * @param int $empresaId
     *
     * @author Alejandro Ocampo
     */
    public function obtenerCategoriaClientePorId(int $categoriaId, int $empresaId): ?CategoriaModel
    {
        return CategoriaModel::query()
            ->where('id', $categoriaId)
            ->where('empresa_id', $empresaId)
            ->first();
    }

    /**
     * Funcion que crea una nueva categoria para el cliente autenticado según el ID de la empresa
     *
     * @param array $data
     *
     * @author Alejandro Ocampo
     */
    public function crearCategoriaCliente(array $data): CategoriaModel
    {
        return CategoriaModel::create([
            'empresa_id'      => $data['empresa_id'],
            'nombre'          => $data['nombre'],
            'descripcion'     => $data['descripcion'] ?? null,
            'estado_id'       => $data['estado_id'],
            'creado_por'      => Auth::id(),
            'actualizado_por' => Auth::id(),
        ]);
    }

    /**
     * Funcion que actualiza una categoria del cliente autenticado según el ID de la categoria
     * proporcionado.
     *
     * @param int $categoriaId
     * @param array $data
     *
     * @author Alejandro Ocampo
     */
    public function actualizarCategoriaCliente(int $categoriaId, array $data): ?CategoriaModel
    {
        $categoria = CategoriaModel::query()
            ->where('id', $categoriaId)
            ->first();

        if (!$categoria) {
            return null;
        }

        $categoria->update([
            'nombre'          => $data['nombre'],
            'descripcion'     => $data['descripcion'] ?? null,
            'estado_id'       => $data['estado_id'],
            'actualizado_por' => Auth::id(),
        ]);

        return $categoria;
    }
    /**
     * Funcion que cambia el estado de una categoria del cliente autenticado según el ID de la categoria
     * proporcionado.
     *
     * @param int $categoriaId
     * @param int $nuevoEstadoId
     *
     * @author Alejandro Ocampo
     */
    public function cambiarEstadoCategoriaCliente(int $categoriaId, int $nuevoEstadoId): ?CategoriaModel
    {
        $categoria = CategoriaModel::query()
            ->where('id', $categoriaId)
            ->first();

        if (!$categoria) {
            return null;
        }

        $categoria->update([
            'estado_id'       => $nuevoEstadoId,
            'actualizado_por' => Auth::id(),
        ]);

        return $categoria;
    }
}
