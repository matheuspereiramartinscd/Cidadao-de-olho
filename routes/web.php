<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeputadoController;

Route::get('/', function () {
    return view('welcome');
});


// Rota para exibir o ranking dos deputados com os maiores reembolsos de um mês específico
Route::get('/ranking-reembolsos/{mes}', [DeputadoController::class, 'rankingReembolsos']);

// Rota para a página principal do ranking de reembolsos
Route::get('/ranking-reembolsos', [DeputadoController::class, 'rankingReembolsosView']);

// Rota para exibir todos os deputados cadastrados no banco de dados
Route::get('/deputados', [DeputadoController::class, 'deputados']);

// Rota para exibir o ranking das redes sociais como JSON (API)
Route::get('/ranking-redes-sociais/json', [DeputadoController::class, 'rankingRedesSociais']);

// Rota para exibir o ranking das redes sociais na interface do frontend
Route::get('/ranking-redes-sociais', function (DeputadoController $controller) {
    return $controller->rankingRedesSociais(false);
});

// Rota para carregar deputados da API da ALMG
Route::get('/get-deputados', [DeputadoController::class, 'getDeputados']);
