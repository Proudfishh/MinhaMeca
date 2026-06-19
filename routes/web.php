<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OficinaAuthController;
use App\Http\Controllers\Auth\ClienteAuthController;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', fn () => view('auth.login'))->name('login');

Route::post('/login/oficina', [OficinaAuthController::class, 'login'])->name('login.oficina');
Route::post('/login/cliente', [ClienteAuthController::class, 'login'])->name('login.cliente');
Route::post('/login/cliente/selecionar', [ClienteAuthController::class, 'selecionarOficina'])->name('login.cliente.selecionar');
Route::post('/logout', function () {
    session()->forget(['oficina_auth', 'oficina_nome', 'tenant_id']);
    return redirect()->route('login');
})->name('logout');
Route::post('/cliente/logout', function () {
    session()->forget(['cliente_auth', 'cliente_id', 'cliente_nome', 'tenant_id', 'oficina_nome', 'cliente_pendente']);
    return redirect()->route('login');
})->name('cliente.logout');
