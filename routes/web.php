<?php

use App\Http\Controllers\ApostaController;
use App\Http\Controllers\ApostadorController;
use App\Http\Controllers\AppUpdateController;
use App\Http\Controllers\CorridaController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\CorridaController::class, 'index'])->name('home');

Route::post('/app/updates/quit-and-install', [AppUpdateController::class, 'quitAndInstall'])
    ->name('app.updates.quit-and-install');
Route::post('/app/updates/check', [AppUpdateController::class, 'check'])
    ->name('app.updates.check');

Route::get('/apostadores/buscar', [ApostadorController::class, 'buscar'])->name('apostadores.buscar');
Route::resource('apostadores', ApostadorController::class);
Route::resource('corridas', CorridaController::class);
Route::resource('apostas', ApostaController::class);

// Rotas de relatórios e impressão
Route::get('/relatorios/corrida/{corridaId}/apostador/{apostadorId}/imprimir', [RelatorioController::class, 'imprimirControleApostador'])
    ->name('relatorios.imprimir.apostador');
Route::get('/relatorios/corrida/{corridaId}/todos-apostadores/imprimir', [RelatorioController::class, 'imprimirControleTodosApostadores'])
    ->name('relatorios.imprimir.todos');
