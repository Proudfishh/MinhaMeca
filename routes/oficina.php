<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Oficina\DashboardController;
use App\Http\Controllers\Oficina\OsController;
use App\Http\Controllers\Oficina\ClienteController;
use App\Http\Controllers\Oficina\VeiculoController;
use App\Http\Controllers\Oficina\EstoqueController;
use App\Http\Controllers\Oficina\FinanceiroController;
use App\Http\Controllers\Oficina\GarantiaController;
use App\Http\Controllers\Oficina\ConfiguracoesController;
use App\Http\Controllers\Oficina\OrcamentosController;

Route::middleware(['auth.oficina', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/os', [OsController::class, 'index'])->name('os.index');
    Route::get('/os/nova', [OsController::class, 'create'])->name('os.create');
    Route::post('/os', [OsController::class, 'store'])->name('os.store');
    Route::get('/os/{id}', [OsController::class, 'show'])->name('os.show');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');

    Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
    Route::get('/veiculos/{id}', [VeiculoController::class, 'show'])->name('veiculos.show');

    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');

    Route::get('/orcamentos',         [OrcamentosController::class, 'index'])->name('orcamentos.index');
    Route::get('/orcamentos/novo',   [OrcamentosController::class, 'create'])->name('orcamentos.create');
    Route::post('/orcamentos',       [OrcamentosController::class, 'store'])->name('orcamentos.store');
    Route::get('/orcamentos/{id}',   [OrcamentosController::class, 'show'])->name('orcamentos.show');
    Route::post('/orcamentos/{id}',  [OrcamentosController::class, 'update'])->name('orcamentos.update');

    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index');

    Route::get('/garantias', [GarantiaController::class, 'index'])->name('garantias.index');

    Route::get('/configuracoes', [ConfiguracoesController::class, 'index'])->name('configuracoes.index');
});
