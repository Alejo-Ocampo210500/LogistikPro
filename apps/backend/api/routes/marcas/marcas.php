<?php


use App\Http\Modules\Marcas\Controllers\MarcasController;
use Illuminate\Support\Facades\Route;

Route::prefix('marcas')->group(function () {
    Route::controller(MarcasController::class)->group(function () {
        Route::get('/listar-marcas', 'listarMarcas');
        Route::post('/crear-marca', 'crearMarca');
    });
});
