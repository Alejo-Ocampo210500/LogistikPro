<?php

use App\Http\Modules\Contact\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('contact')->group(function () {
    Route::controller(ContactController::class)->group(function () {
        Route::post('/suggestions', 'store');
    });
});
