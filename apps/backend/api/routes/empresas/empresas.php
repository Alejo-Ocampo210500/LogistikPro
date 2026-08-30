<?php

use App\Http\Modules\Empresas\Controllers\EmpresasController;
use Illuminate\Support\Facades\Route;

Route::prefix('empresas')->group(function () {
    Route::controller(EmpresasController::class)->group(function () {
        Route::get('/listar-empresas', 'listarEmpresas');
        Route::post('/crear-empresa', 'crearEmpresa');
    });
});
