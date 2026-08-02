<?php

namespace App\Http\Controllers\Marcas\Repositories;

use App\Models\Marcas\marca as MarcaModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MarcasClientesRepository
{
    /**
     * Funcion que obtiene las marcas del cliente autenticado según el ID de la empresa proporcionado.
     *
     * @param int $empresaId
     *
     * @author Alejandro Ocampo
     */
    public function obtenerMarcasCliente(int $empresaId): Collection
    {
        return MarcaModel::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Funcion que obtiene una marca del cliente autenticado según el ID de la marca
     * y el ID de la empresa proporcionados.
     *
     * @param int $marcaId
     * @param int $empresaId
     *
     * @author Alejandro Ocampo
     */
    public function obtenerMarcaClientePorId(int $marcaId, int $empresaId): ?MarcaModel
    {
        return MarcaModel::query()
            ->where('id', $marcaId)
            ->where('empresa_id', $empresaId)
            ->first();
    }

    /**
     * Funcion que crea una nueva marca para el cliente autenticado según el ID de la empresa
     * proporcionado y los datos de la marca.
     *
     * @param int $empresaId
     * @param array $data
     *
     * @author Alejandro Ocampo
     */
    public function crearMarcasCliente(int $empresaId, array $data)
    {
        $marca = new MarcaModel();
        $marca->empresa_id = $empresaId;
        $marca->nombre = $data['nombre'];
        $marca->descripcion = $data['descripcion'] ?? null;
        $marca->logo = $data['logo'] ?? null;
        $marca->sitio_web = $data['sitio_web'] ?? null;
        $marca->estado_id = $data['estado_id'] ?? null;
        $marca->creado_por = Auth::id();
        $marca->actualizado_por = Auth::id();
        $marca->save();

        return $marca;
    }

    /**
     * Funcion que actualiza una marca del cliente autenticado según el ID de la marca
     * y el ID de la empresa proporcionados.
     *
     * @param int $empresaId
     * @param int $marcaId
     * @param array $data
     *
     * @author Alejandro Ocampo
     */
    public function actualizarMarcaCliente(int $empresaId, int $marcaId, array $data): ?MarcaModel
    {
        $marca = MarcaModel::query()
            ->where('id', $marcaId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$marca) {
            return null;
        }

        $marca->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'logo' => $data['logo'] ?? null,
            'sitio_web' => $data['sitio_web'] ?? null,
            'estado_id' => $data['estado_id'] ?? null,
            'actualizado_por' => Auth::id(),
        ]);

        return $marca;
    }

    /**
     * Funcion que cambia el estado de una marca del cliente autenticado según el ID de la marca
     * y el ID de la empresa proporcionados.
     *
     * @param int $empresaId
     * @param int $marcaId
     * @param int $nuevoEstadoId
     *
     * @author Alejandro Ocampo
     */
    public function cambiarEstadoMarcaCliente(int $empresaId, int $marcaId, int $nuevoEstadoId): ?MarcaModel
    {
        $marca = MarcaModel::query()
            ->where('id', $marcaId)
            ->where('empresa_id', $empresaId)
            ->first();

        if (!$marca) {
            return null;
        }

        $marca->estado_id = $nuevoEstadoId;
        $marca->actualizado_por = Auth::id();
        $marca->save();

        return $marca;
    }
}
