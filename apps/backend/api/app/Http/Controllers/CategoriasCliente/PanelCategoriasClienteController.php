<?php

namespace App\Http\Controllers\CategoriasCliente;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CategoriasCliente\Repositories\CategoriasClientesRepository;
use App\Http\Controllers\CategoriasCliente\CrearCategoriaClienteRequest;
use App\Models\Estados\Estado;

class PanelCategoriasClienteController extends Controller
{
    public function __construct(protected CategoriasClientesRepository $CategoriasClientesRepository) {}

    /**
     * Funcion del controlador que recibe la solicitud para obtener las categorias del cliente autenticado
     * y devuelve una respuesta JSON con las categorias obtenidas o un mensaje de error en caso de que ocurra algún problema.
     *
     * @author Alejandro Ocampo
     */
    public function obtenerCategoriasCliente()
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $categorias = $this->CategoriasClientesRepository->obtenerCategoriasCliente($empresaId);

            return response()->json([
                'mensaje' => 'Categorias obtenidas correctamente.',
                'categorias' => $categorias,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las categorias.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Funciond del controlador que recibe la solicitud para obtener una categoria del cliente autenticado por su ID
     * y devuelve una respuesta JSON con la categoria obtenida o un mensaje de error en caso de que ocurra algún problema.
     *
     * @param int $categoriaId
     *
     * @author Alejandro Ocampo
     */
    public function obtenerCategoriaClientePorId(int $categoriaId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $categoria = $this->CategoriasClientesRepository->obtenerCategoriaClientePorId($categoriaId, $empresaId);

            if (!$categoria) {
                return response()->json([
                    'mensaje' => 'Categoria no encontrada para la empresa autenticada.',
                ], 404);
            }

            return response()->json([
                'mensaje' => 'Categoria obtenida correctamente.',
                'categoria' => $categoria,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al obtener las categorías.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Funcion del controlador que recibe la solicitud para crear una categoria del cliente autenticado
     * y devuelve una respuesta JSON con la categoria creada o un mensaje de error en caso de que ocurra algún problema.
     *
     * @param CrearCategoriaClienteRequest $crearCategoriaClienteRequest
     *
     * @author Alejandro Ocampo
     */
    public function crearCategoriaCliente(CrearCategoriaClienteRequest $crearCategoriaClienteRequest)
    {
        try {
            $payload = $crearCategoriaClienteRequest->validated();
            $payload['empresa_id'] = (int) ($crearCategoriaClienteRequest->user()?->empresa_id ?? 0);

            if ($payload['empresa_id'] <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $estadoId = isset($payload['estado_id']) ? (int) $payload['estado_id'] : 0;
            $estadoValido = $estadoId > 0
                ? Estado::query()->where('id', $estadoId)->first()
                : null;

            if ($estadoId > 0 && !$estadoValido) {
                return response()->json([
                    'mensaje' => 'El estado seleccionado no existe.',
                ], 422);
            }

            if ($estadoValido) {
                $nombreEstado = strtolower(trim((string) $estadoValido->nombre));

                if (!in_array($nombreEstado, ['activo', 'inactivo'], true)) {
                    return response()->json([
                        'mensaje' => 'Solo se permiten estados Activo o Inactivo para categorías.',
                    ], 422);
                }
            }

            if (!$estadoValido) {
                $estadoValido = Estado::query()
                    ->where('nombre', 'Activo')
                    ->orWhere('nombre', 'ACTIVO')
                    ->orWhere('nombre', 'activo')
                    ->first();
            }

            if (!$estadoValido) {
                return response()->json([
                    'mensaje' => 'No existe un estado Activo configurado para crear la categoría.',
                ], 422);
            }

            $payload['estado_id'] = (int) $estadoValido->id;

            $crearCategoriasClientes = $this->CategoriasClientesRepository->crearCategoriaCliente($payload);
            return response()->json([
                'mensaje' => 'Categoria creada correctamente.',
                'categoria' => $crearCategoriasClientes,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al crear la categoría.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Funcion del controlador que recibe la solicitud para actualizar una categoria del cliente autenticado
     * y devuelve una respuesta JSON con la categoria actualizada o un mensaje de error en caso de que ocurra algún problema.
     *
     * @param int $categoriaId
     * @param CrearCategoriaClienteRequest $request
     *
     * @author Alejandro Ocampo
     */
    public function actualizarCategoriaCliente(int $categoriaId, CrearCategoriaClienteRequest $request)
    {
        try {
            $empresaId = (int) ($request->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $payload = $request->validated();
            $payload['empresa_id'] = $empresaId;

            $categoriaActualizada = $this->CategoriasClientesRepository->actualizarCategoriaCliente($categoriaId, $payload);

            if (!$categoriaActualizada) {
                return response()->json([
                    'mensaje' => 'No se pudo actualizar la categoría. Verifique que exista y pertenezca a la empresa.',
                ], 404);
            }

            return response()->json([
                'mensaje' => 'Categoría actualizada correctamente.',
                'categoria' => $categoriaActualizada,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al actualizar la categoría.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Funcion del controlador que recibe la solicitud para cambiar el estado de una categoria del cliente autenticado
     * y devuelve una respuesta JSON con la categoria actualizada o un mensaje de error en caso de que ocurra algún problema.
     *
     * @param int $categoriaId
     *
     * @author Alejandro Ocampo
     */
    public function cambiarEstadoCategoriaCliente(int $categoriaId)
    {
        try {
            $empresaId = (int) (request()->user()?->empresa_id ?? 0);

            if ($empresaId <= 0) {
                return response()->json([
                    'mensaje' => 'No se pudo resolver la empresa del usuario autenticado.',
                ], 422);
            }

            $categoria = $this->CategoriasClientesRepository->obtenerCategoriaClientePorId($categoriaId, $empresaId);

            if (!$categoria) {
                return response()->json([
                    'mensaje' => 'Categoria no encontrada para la empresa autenticada.',
                ], 404);
            }

            $nuevoEstado = $categoria->estado_id === 1 ? 2 : 1;
            $categoriaActualizada = $this->CategoriasClientesRepository->cambiarEstadoCategoriaCliente($categoriaId, $nuevoEstado);

            return response()->json([
                'mensaje' => 'Estado de la categoría actualizado correctamente.',
                'categoria' => $categoriaActualizada,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensaje' => 'Error al cambiar el estado de la categoría.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

}
