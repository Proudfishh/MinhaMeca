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
use App\Http\Controllers\Oficina\EquipeController;

Route::middleware(['auth.oficina', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.ver');

    Route::get('/os', [OsController::class, 'index'])->name('os.index')->middleware('permission:os.ver');
    Route::get('/os/nova', [OsController::class, 'create'])->name('os.create')->middleware('permission:os.criar');
    Route::post('/os', [OsController::class, 'store'])->name('os.store')->middleware('permission:os.criar');
    Route::get('/os/{id}', [OsController::class, 'show'])->name('os.show')->middleware('permission:os.ver');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('permission:clientes.ver');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show')->middleware('permission:clientes.ver');

    Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index')->middleware('permission:veiculos.ver');
    Route::get('/veiculos/{id}', [VeiculoController::class, 'show'])->name('veiculos.show')->middleware('permission:veiculos.ver');

    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index')->middleware('permission:estoque.ver');

    Route::get('/orcamentos',         [OrcamentosController::class, 'index'])->name('orcamentos.index')->middleware('permission:orcamentos.ver');
    Route::get('/orcamentos/novo',   [OrcamentosController::class, 'create'])->name('orcamentos.create')->middleware('permission:orcamentos.criar');
    Route::post('/orcamentos',       [OrcamentosController::class, 'store'])->name('orcamentos.store')->middleware('permission:orcamentos.criar');
    Route::get('/orcamentos/{id}',        [OrcamentosController::class, 'show'])->name('orcamentos.show')->middleware('permission:orcamentos.ver');
    Route::get('/orcamentos/{id}/editar', [OrcamentosController::class, 'edit'])->name('orcamentos.edit')->middleware('permission:orcamentos.editar');
    Route::post('/orcamentos/{id}',       [OrcamentosController::class, 'update'])->name('orcamentos.update')->middleware('permission:orcamentos.editar');

    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index')->middleware('permission:financeiro.ver');

    Route::get('/garantias', [GarantiaController::class, 'index'])->name('garantias.index')->middleware('permission:garantias.ver');

    Route::get('/configuracoes', [ConfiguracoesController::class, 'index'])->name('configuracoes.index')->middleware('permission:configuracoes.ver');

    Route::middleware('permission:configuracoes.editar')->prefix('configuracoes/equipe')->name('equipe.')->group(function () {
        Route::post('/', [EquipeController::class, 'store'])->name('store');
        Route::put('/{user}', [EquipeController::class, 'update'])->name('update');
        Route::patch('/{user}/toggle', [EquipeController::class, 'toggleAtivo'])->name('toggle');
    });
});
