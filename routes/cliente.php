<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cliente\VeiculoController;
use App\Http\Controllers\Cliente\OsController;
use App\Http\Controllers\Cliente\HistoricoController;

Route::middleware(['auth.cliente', 'tenant'])->group(function () {
    Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');

    Route::get('/os/{id}', [OsController::class, 'show'])->name('os.show');

    Route::get('/historico', [HistoricoController::class, 'index'])->name('historico.index');
});
