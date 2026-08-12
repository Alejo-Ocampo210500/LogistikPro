<?php

use App\Http\Controllers\Cajas\PanelCajasClienteController;
use App\Http\Controllers\CategoriasCliente\PanelCategoriasClienteController;
use App\Http\Controllers\Ciudades\PanelCiudadesClienteController;
use App\Http\Controllers\Clientes\PanelClientesClienteController;
use App\Http\Controllers\ControlCajas\PanelControlCajasController;
use App\Http\Controllers\Departamentos\PanelDepartamentosClienteController;
use App\Http\Middleware\EnsureCliente;
use App\Http\Controllers\Planes\PlanesController;
use App\Http\Controllers\Seguridad\Auth\Controllers\LoginController;
use App\Http\Controllers\Superadmin\PanelSuperadminController;
use App\Http\Controllers\Estados\EstadoController;
use App\Http\Controllers\Impuesto\PanelImpuestosClienteController;
use App\Http\Controllers\Mantenimiento\PanelMantenimientoController;
use App\Http\Controllers\Marcas\PanelMarcasClienteController;
use App\Http\Controllers\MetodosPago\PanelMetodosPagoController;
use App\Http\Controllers\Pagos\PanelPagosEmpresaController;
use App\Http\Controllers\Paises\PanelPaisesClienteController;
use App\Http\Controllers\Productos\PanelProductosClienteController;
use App\Http\Controllers\Provedores\PanelProvedoresClienteController;
use App\Http\Controllers\Sucursales\PanelSucursalesClienteController;
use App\Http\Controllers\Suscripciones\PanelSuscripcionesController;
use App\Http\Controllers\TipoDocumento\PanelTipoDocumentoClienteController;
use App\Http\Controllers\UnidadesMedida\PanelUnidadMedidaController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::get('/estados', [EstadoController::class, 'index'])
    ->middleware(['auth:sanctum']);

Route::get('/superadmin/panel', [PanelSuperadminController::class, 'index'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('/superadmin/empresas/{empresa}', [PanelSuperadminController::class, 'show'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::post('/superadmin/empresas', [PanelSuperadminController::class, 'store'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('/superadmin/empresas/{empresa}', [PanelSuperadminController::class, 'update'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::post('/planes', [PlanesController::class, 'crearPlan'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('/planes/{plan}', [PlanesController::class, 'actualizarPlan'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('/superadmin/empresas/{empresa}/cambiar-estado', [PanelSuperadminController::class, 'cambiarEstado'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('/superadmin/empresas/{empresa}/cambiar-password', [PanelSuperadminController::class, 'cambiarPassword'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('/superadmin/usuarios', [PanelSuperadminController::class, 'obtenerUsuarios'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('mantenimiento/editar-usuarios-globales/{usuario}', [PanelMantenimientoController::class, 'actualizarUsuarioGlobal'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('mantenimiento/estados', [PanelMantenimientoController::class, 'obtenerEstados'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('suscripciones', [PanelSuscripcionesController::class, 'obtenerSuscripciones'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('empresas/pagos/{empresa}', [PanelPagosEmpresaController::class, 'listarPagosEmpresa'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('suscripciones/pagos-planes/{empresa}', [PanelPagosEmpresaController::class, 'listarPagosPlanEmpresa'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('/planes/listar', [PlanesController::class, 'listarPlanes'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::put('suscripciones/confirmar-pago', [PanelPagosEmpresaController::class, 'confirmarPago'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::post('suscripciones/registrar-pago-manual', [PanelPagosEmpresaController::class, 'registrarPagoManual'])
    ->middleware(['auth:sanctum', 'superadmin']);

Route::get('/metodos-pago/listar', [PanelMetodosPagoController::class, 'listarMetodosPago'])
    ->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/categorias', [PanelCategoriasClienteController::class, 'obtenerCategoriasCliente']);
    Route::post('/categorias/crear', [PanelCategoriasClienteController::class, 'crearCategoriaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/categorias/{categoriaId}', [PanelCategoriasClienteController::class, 'obtenerCategoriaClientePorId']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::put('/categorias/{categoriaId}/actualizar', [PanelCategoriasClienteController::class, 'actualizarCategoriaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::put('/categorias/{categoriaId}/cambiarEstado', [PanelCategoriasClienteController::class, 'cambiarEstadoCategoriaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/marcas', [PanelMarcasClienteController::class, 'obtenerMarcasCliente']);
    Route::post('/marcas/crearMarcasCliente', [PanelMarcasClienteController::class, 'crearMarcasCliente']);
    Route::put('/marcas/{marcasId}/actualizar', [PanelMarcasClienteController::class, 'actualizarMarcaCliente']);
    Route::post('/marcas/{marcasId}/cambiarEstado', [PanelMarcasClienteController::class, 'cambiarEstadoMarcaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/unidades-medida', [PanelUnidadMedidaController::class, 'obtenerUnidadesMedidaCliente']);
    Route::post('/unidades-medida/crear', [PanelUnidadMedidaController::class, 'crearUnidadMedidaCliente']);
    Route::put('/unidades-medida/{unidadId}/actualizar', [PanelUnidadMedidaController::class, 'actualizarUnidadMedidaCliente']);
    Route::post('/unidades-medida/{unidadId}/cambiarEstado', [PanelUnidadMedidaController::class, 'cambiarEstadoUnidadMedidaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/paises', [PanelPaisesClienteController::class, 'obtenerPaisesCliente']);
});

Route::get('/departamentos', [PanelDepartamentosClienteController::class, 'obtenerDepartamentosCliente'])
    ->middleware(['auth:sanctum']);

Route::get('/ciudades', [PanelCiudadesClienteController::class, 'obtenerCiudadesCliente'])
    ->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/tipo-documento', [PanelTipoDocumentoClienteController::class, 'obtenerTipoDocumentosCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/provedores', [PanelProvedoresClienteController::class, 'obtenerProvedoresCliente']);
    Route::post('/provedores/crear', [PanelProvedoresClienteController::class, 'crearProvedorCliente']);
    Route::put('/provedores/{provedorId}/actualizar', [PanelProvedoresClienteController::class, 'actualizarProvedorCliente']);
    Route::post('/provedores/{provedorId}/cambiarEstado', [PanelProvedoresClienteController::class, 'cambiarEstadoProvedorCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/impuestos', [PanelImpuestosClienteController::class, 'obtenerImpuestosCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/productos', [PanelProductosClienteController::class, 'obtenerProductosCliente']);
    Route::post('/productos/crear', [PanelProductosClienteController::class, 'crearProductoCliente']);
    Route::put('/productos/{productoId}/actualizar', [PanelProductosClienteController::class, 'actualizarProductoCliente']);
    Route::post('/productos/{productoId}/cambiarEstado', [PanelProductosClienteController::class, 'cambiarEstadoProductoCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/clientes', [PanelClientesClienteController::class, 'obtenerClientesCliente']);
    Route::post('/clientes/crear', [PanelClientesClienteController::class, 'crearClienteCliente']);
    Route::put('/clientes/{clienteId}/actualizar', [PanelClientesClienteController::class, 'actualizarClienteCliente']);
    Route::post('/clientes/{clienteId}/cambiarEstado', [PanelClientesClienteController::class, 'cambiarEstadoClienteCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/sucursales', [PanelSucursalesClienteController::class, 'obtenerSucursalesCliente']);
    Route::post('/sucursales/crear', [PanelSucursalesClienteController::class, 'crearSucursalCliente']);
    Route::put('/sucursales/{sucursalId}/actualizar', [PanelSucursalesClienteController::class, 'actualizarSucursalCliente']);
    Route::post('/sucursales/{sucursalId}/cambiarEstado', [PanelSucursalesClienteController::class, 'cambiarEstadoSucursalCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/cajas', [PanelCajasClienteController::class, 'obtenerCajasCliente']);
    Route::post('/cajas/crear', [PanelCajasClienteController::class, 'crearCajaCliente']);
    Route::put('/cajas/{cajaId}/actualizar', [PanelCajasClienteController::class, 'actualizarCajaCliente']);
    Route::post('/cajas/{cajaId}/cambiarEstado', [PanelCajasClienteController::class, 'cambiarEstadoCajaCliente']);
});

Route::middleware(['auth:sanctum', EnsureCliente::class])->group(function () {
    Route::get('/control-cajas', [PanelControlCajasController::class, 'obtenerControlCajasCliente']);
    Route::post('/control-cajas/abrir', [PanelControlCajasController::class, 'abrirControlCajaCliente']);
    Route::put('/control-cajas/{controlCajaId}/cerrar', [PanelControlCajasController::class, 'cerrarControlCajaCliente']);
    Route::post('/control-cajas/{controlCajaId}/anular', [PanelControlCajasController::class, 'anularControlCajaCliente']);
});
