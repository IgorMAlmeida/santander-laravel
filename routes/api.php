<?php

use App\Http\Controllers\SantanderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('santander')->group(function () {
    Route::post('esteira', [SantanderController::class, 'Esteira']);
});