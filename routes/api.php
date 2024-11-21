<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeputadoController;

Route::get('/ranking-reembolsos', [DeputadoController::class, 'rankingReembolsos']);
Route::get('/ranking-redes-sociais', [DeputadoController::class, 'rankingRedesSociais']);
Route::get('/get-deputados', [DeputadoController::class, 'getDeputados']);

