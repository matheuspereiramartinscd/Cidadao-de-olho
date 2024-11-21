<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeputadoController;

Route::get('/', function () {
    return view('welcome');
});

// Rota para atualizar os reembolsos de janeiro de 2019


// Rota para o ranking dos 5 deputados com mais reembolsos de janeiro de 2019
Route::get('/ranking-reembolsos-janeiro', [DeputadoController::class, 'rankingReembolsosJaneiro']);
Route::get('/ranking-reembolsos-fevereiro', [DeputadoController::class, 'rankingReembolsosFevereiro']);
Route::get('/ranking-reembolsos-marco', [DeputadoController::class, 'rankingReembolsosMarco']);
Route::get('/ranking-reembolsos-abril', [DeputadoController::class, 'rankingReembolsosAbril']);
Route::get('/ranking-reembolsos-maio', [DeputadoController::class, 'rankingReembolsosMaio']);
Route::get('/ranking-reembolsos-junho', [DeputadoController::class, 'rankingReembolsosJunho']);
Route::get('/ranking-reembolsos-julho', [DeputadoController::class, 'rankingReembolsosJulho']);
Route::get('/ranking-reembolsos-agosto', [DeputadoController::class, 'rankingReembolsosAgosto']);
Route::get('/ranking-reembolsos-setembro', [DeputadoController::class, 'rankingReembolsosSetembro']);
Route::get('/ranking-reembolsos-outubro', [DeputadoController::class, 'rankingReembolsosOutubro']);
Route::get('/ranking-reembolsos-novembro', [DeputadoController::class, 'rankingReembolsosNovembro']);
Route::get('/ranking-reembolsos-dezembro', [DeputadoController::class, 'rankingReembolsosDezembro']);


// Rota para o ranking de redes sociais (deputados mais ativos nas redes sociais)
Route::get('/ranking-redes-sociais', [DeputadoController::class, 'rankingRedesSociais']);

// Rota para carregar deputados da API da ALMG
Route::get('/get-deputados', [DeputadoController::class, 'getDeputados']);
// Em routes/api.php
Route::get('/deputado/total-reembolsado/4458', [DeputadoController::class, 'totalReembolsadoDeputado4458']);
