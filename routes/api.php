<?php

use App\Http\Controllers\SantanderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getEsteira', [SantanderController::class, 'Esteira']);
