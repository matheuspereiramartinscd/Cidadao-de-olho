<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeputadoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
});

/**
 * Rota para exibir o ranking dos deputados com os maiores reembolsos de janeiro de 2019.
 * A lógica de cálculo dos reembolsos é definida no método 'rankingReembolsosJaneiro' do 'DeputadoController'.
 */
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

// Rota para exibir todos os deputados cadastrados no banco de dados
Route::get('/deputados', [DeputadoController::class, 'deputados']);

// Rota para exibir a página do ranking de reembolsos
Route::get('/ranking-reembolsos', [DeputadoController::class, 'rankingReembolsosView']);

/**
 * Rota para exibir o ranking dos deputados mais ativos nas redes sociais.
 * O método 'rankingRedesSociais' do 'DeputadoController' é responsável por calcular esse ranking.
 */
Route::get('/ranking-redes-sociais', [DeputadoController::class, 'rankingRedesSociais']);

// Rota para exibir o ranking das redes sociais na interface do frontend
Route::get('/ranking-redes-sociais-front', [DeputadoController::class, 'rankingRedesSociaisView']);

// Rota para carregar deputados da API da ALMG
Route::get('/get-deputados', [DeputadoController::class, 'getDeputados']);

// Rota para consultar o total de reembolsos de um deputado específico (ID 4458) em 2019
Route::get('/deputado/total-reembolsado/4458', [DeputadoController::class, 'totalReembolsadoDeputado4458']);
