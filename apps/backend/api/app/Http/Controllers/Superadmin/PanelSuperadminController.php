<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Notificaciones\Services\NotificacionesSistemaService;
use App\Http\Requests\Superadmin\StoreEmpresaRequest;
use App\Http\Requests\Superadmin\UpdateEmpresaRequest;
use App\Models\Departamentos\Departamento;
use App\Models\Empresas\Empresa;
use App\Models\Planes\Plan;
use App\Models\Seguridad\Rol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PanelSuperadminController extends Controller
{
    public function __construct(
        protected SuperadminService $superadminService,
        protected NotificacionesSistemaService $notificacionesService
    ) {}
    public function index(): JsonResponse
    {
        $activo = \App\Models\Estados\Estado::where('nombre', 'Activo')->first();
        $inactivo = \App\Models\Estados\Estado::where('nombre', 'inactivo')->first();
        $activoId = $activo ? $activo->id : 1;
        $inactivoId = $inactivo ? $inactivo->id : 2;

        $empresas = Empresa::query()
            ->with(['estado', 'departamento:id,nombre', 'ciudad:id,nombre'])
            ->withCount([
                'users',
                'users as usuarios_activos_count' => function ($query) use ($activoId) {
                    $query->where('estado_id', $activoId);
                },
                'users as usuarios_inactivos_count' => function ($query) use ($inactivoId) {
                    $query->where('estado_id', $inactivoId);
                },
            ])
            ->latest('id')
            ->get();

        $usuariosTotal = User::count();
        $empresasActivas = Empresa::where('estado_id', $activoId)->count();
        $superadminsTotal = User::whereHas('rol', function ($query) {
            $query->whereRaw('LOWER(nombre) = ? OR LOWER(nombre) = ?', ['superadmin', 'superadministrador']);
        })->count();

        return response()->json([
            'estadisticas' => [
                'empresas_total' => $empresas->count(),
                'empresas_activas' => $empresasActivas,
                'usuarios_total' => $usuariosTotal,
                'superadmins_total' => $superadminsTotal,
            ],
            'empresas' => $empresas,
            'planes' => Plan::with(['estado'])->orderBy('id', 'desc')->get(),
        ]);
    }

    public function show(Empresa $empresa): JsonResponse
    {
        $activo = \App\Models\Estados\Estado::where('nombre', 'Activo')->first();
        $inactivo = \App\Models\Estados\Estado::where('nombre', 'inactivo')->first();
        $activoId = $activo ? $activo->id : 1;
        $inactivoId = $inactivo ? $inactivo->id : 2;

        $empresa->load(['estado']);
        $empresa->loadCount([
            'users',
            'users as usuarios_activos_count' => function ($query) use ($activoId) {
                $query->where('estado_id', $activoId);
            },
            'users as usuarios_inactivos_count' => function ($query) use ($inactivoId) {
                $query->where('estado_id', $inactivoId);
            },
        ]);

        $usuarios = $empresa->users()
            ->with(['rol', 'estado'])
            ->latest('id')
            ->get();

        return response()->json([
            'empresa' => $empresa,
            'estadisticas' => [
                'usuarios_total' => $empresa->users_count,
                'usuarios_activos' => $empresa->usuarios_activos_count,
                'usuarios_inactivos' => $empresa->usuarios_inactivos_count,
            ],
            'usuarios' => $usuarios,
            'modulos' => $this->modulosPorPlan($empresa->plan),
        ]);
    }

    public function store(StoreEmpresaRequest $request): JsonResponse
    {
        $estadoActivo = \App\Models\Estados\Estado::where('nombre', 'Activo')->first();
        $estadoActivoId = $estadoActivo ? $estadoActivo->id : 1;

        $rolAdministrador = Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Administrador de la empresa',
                'estado_id' => $estadoActivoId,
            ]
        );

        $empresa = DB::transaction(function () use ($request, $rolAdministrador, $estadoActivoId) {
            $plan = $request->filled('plan_id')
                ? Plan::findOrFail($request->input('plan_id'))
                : Plan::query()->orderBy('id')->firstOrFail();
            $departamento = Departamento::findOrFail($request->input('departamento_id'));
            $ciudad = \App\Models\Ciudades\ciudad::findOrFail($request->input('ciudad_id'));

            $empresa = Empresa::create([
                'nombre_comercial' => $request->input('nombre_comercial'),
                'razon_social' => $request->input('razon_social'),
                'nit' => $request->input('nit'),
                'email' => $request->input('email'),
                'telefono' => $request->input('telefono'),
                'direccion' => $request->input('direccion'),
                'departamento_id' => $departamento->id,
                'ciudad_id' => $ciudad->id,
                'departamento' => $departamento->nombre,
                'ciudad' => $ciudad->nombre,
                'logo' => $request->input('logo'),
                'estado_id' => $estadoActivoId,
                'plan' => $plan->nombre,
                'plan_id' => $plan->id,
                'fecha_vencimiento' => $request->input('fecha_vencimiento'),
            ]);

            $usuario = User::create([
                'empresa_id' => $empresa->id,
                'rol_id' => $rolAdministrador->id,
                'nombre' => $request->input('admin_nombre'),
                'apellido' => $request->input('admin_apellido'),
                'email' => $request->input('admin_email'),
                'telefono' => $request->input('admin_telefono'),
                'password' => $request->input('admin_password'),
                'estado_id' => $estadoActivoId,
            ]);

            $empresa->setRelation('usuario_inicial', $usuario);

            return $empresa;
        });

        $usuarioInicial = $empresa->getRelation('usuario_inicial');

        $this->notificacionesService->registrarEvento('empresa_registrada', [
            'empresa_id' => $empresa->id,
            'empresa_nombre' => $empresa->nombre_comercial,
            'usuario_actor_id' => Auth::id(),
            'destino_modulo' => 'empresas-listado',
            'destino_id' => (string) $empresa->id,
        ]);

        if ($usuarioInicial) {
            $this->notificacionesService->registrarEvento('usuario_creado', [
                'empresa_id' => $empresa->id,
                'empresa_nombre' => $empresa->nombre_comercial,
                'usuario_actor_id' => Auth::id(),
                'usuario_nombre' => trim(($usuarioInicial->nombre ?? '') . ' ' . ($usuarioInicial->apellido ?? '')),
                'destino_modulo' => 'usuarios-globales',
                'destino_id' => (string) $usuarioInicial->id,
            ]);
        }

        return response()->json([
            'mensaje' => 'Empresa creada correctamente.',
            'empresa' => $empresa->loadCount('users'),
        ], 201);
    }

    public function update(UpdateEmpresaRequest $request, Empresa $empresa): JsonResponse
    {
        $plan = $request->filled('plan_id')
            ? Plan::findOrFail($request->input('plan_id'))
            : null;
        $departamento = Departamento::findOrFail($request->input('departamento_id'));
        $ciudad = \App\Models\Ciudades\ciudad::findOrFail($request->input('ciudad_id'));
        $activo = \App\Models\Estados\Estado::where('nombre', 'Activo')->first();
        $activoId = $activo ? $activo->id : 1;

        $payload = [
            'nombre_comercial' => $request->input('nombre_comercial'),
            'razon_social' => $request->input('razon_social'),
            'nit' => $request->input('nit'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
            'direccion' => $request->input('direccion'),
            'departamento_id' => $departamento->id,
            'ciudad_id' => $ciudad->id,
            'departamento' => $departamento->nombre,
            'ciudad' => $ciudad->nombre,
            'logo' => $request->input('logo'),
            'estado_id' => $request->input('estado_id'),
            'fecha_vencimiento' => $request->input('fecha_vencimiento'),
        ];

        if ($plan) {
            $payload['plan'] = $plan->nombre;
            $payload['plan_id'] = $plan->id;
        }

        $empresa->update($payload);

        return response()->json([
            'mensaje' => 'Empresa actualizada correctamente.',
            'empresa' => $empresa->fresh()->load(['estado'])->loadCount('users'),
        ]);
    }

    private function modulosPorPlan(?string $plan): array
    {
        $catalogo = [
            [
                'codigo' => 'ventas',
                'nombre' => 'Ventas',
                'descripcion' => 'Gestión de cotizaciones, pedidos y facturación.',
            ],
            [
                'codigo' => 'clientes',
                'nombre' => 'Clientes',
                'descripcion' => 'Registro y seguimiento de clientes.',
            ],
            [
                'codigo' => 'inventario',
                'nombre' => 'Inventario',
                'descripcion' => 'Control de stock y movimientos.',
            ],
            [
                'codigo' => 'compras',
                'nombre' => 'Compras',
                'descripcion' => 'Pedidos a proveedores y abastecimiento.',
            ],
            [
                'codigo' => 'reportes',
                'nombre' => 'Reportes',
                'descripcion' => 'Indicadores operativos y financieros.',
            ],
            [
                'codigo' => 'seguridad',
                'nombre' => 'Seguridad',
                'descripcion' => 'Roles, permisos y control de accesos.',
            ],
            [
                'codigo' => 'auditoria',
                'nombre' => 'Auditoría',
                'descripcion' => 'Trazabilidad de acciones del sistema.',
            ],
            [
                'codigo' => 'integraciones',
                'nombre' => 'Integraciones',
                'descripcion' => 'Conexión con servicios externos.',
            ],
        ];

        $habilitadosPorPlan = [
            'basico' => ['ventas', 'clientes'],
            'profesional' => ['ventas', 'clientes', 'inventario', 'compras', 'reportes'],
            'empresarial' => ['ventas', 'clientes', 'inventario', 'compras', 'reportes', 'seguridad'],
            'plataforma' => ['ventas', 'clientes', 'inventario', 'compras', 'reportes', 'seguridad', 'auditoria', 'integraciones'],
        ];

        $habilitados = $habilitadosPorPlan[$plan] ?? $habilitadosPorPlan['basico'];

        return array_map(function (array $modulo) use ($habilitados) {
            $modulo['habilitado'] = in_array($modulo['codigo'], $habilitados, true);

            return $modulo;
        }, $catalogo);
    }

    public function cambiarEstado(Request $request, Empresa $empresa)
    {
        try {
            $cambiarEstado = $this->superadminService->cambiarEstado([
                'empresa' => $empresa,
                'estado' => !$empresa->estado,
            ]);
            return response()->json($cambiarEstado, 200);
        } catch (\Throwable $th) {
            return response([
                'mensaje' => 'Error al cambiar el estado de la empresa. Inténtalo de nuevo.',
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    public function cambiarPassword(Request $request, Empresa $empresa): JsonResponse
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, una letra mayúscula y un número.',
        ]);

        $rolAdministrador = Rol::whereRaw('LOWER(nombre) = ?', ['administrador'])->first();

        // Buscar el usuario con rol de Administrador para esa empresa
        $usuario = User::where('empresa_id', $empresa->id)
            ->when($rolAdministrador, function ($query, $rol) {
                $query->where('rol_id', $rol->id);
            })
            ->first();

        // Si no se encuentra, tomamos el primer usuario de la empresa (generalmente es el inicial/administrador)
        if (!$usuario) {
            $usuario = User::where('empresa_id', $empresa->id)->first();
        }

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'No se encontró ningún usuario asociado a esta empresa para cambiar la contraseña.'
            ], 404);
        }

        $usuario->update([
            'password' => $request->input('password')
        ]);

        $this->notificacionesService->registrarEvento('accion_administrativa', [
            'empresa_id' => $empresa->id,
            'empresa_nombre' => $empresa->nombre_comercial,
            'usuario_actor_id' => Auth::id(),
            'mensaje' => 'Se actualizo la contrasena del administrador de la empresa ' . $empresa->nombre_comercial . '.',
            'destino_modulo' => 'usuarios-globales',
            'destino_id' => (string) $usuario->id,
        ]);

        return response()->json([
            'mensaje' => 'Contraseña del administrador actualizada correctamente.',
            'usuario_email' => $usuario->email
        ]);
    }

    public function obtenerUsuarios()
    {
        $usuarios = User::with([
            'empresa:id,nombre_comercial',
            'rol:id,nombre',
            'estado:id,nombre'
        ])->get();

        return response()->json($usuarios, 200);
    }
}
