<?php

use App\Http\Controllers\ApostaController;
use App\Http\Controllers\ApostadorController;
use App\Http\Controllers\CorridaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\CorridaController::class, 'index'])->name('home');

Route::resource('apostadores', ApostadorController::class);
Route::resource('corridas', CorridaController::class);
Route::resource('apostas', ApostaController::class);
