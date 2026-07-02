<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OficinaAuthController;
use App\Http\Controllers\Auth\ClienteAuthController;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', function () {
    // no-store evita que o botão "voltar" (ou o bfcache do celular) restaure
    // esta página com um token CSRF já invalidado por um login anterior.
    return response()->view('auth.login')->header('Cache-Control', 'no-store, private');
})->name('login');

Route::post('/login/oficina', [OficinaAuthController::class, 'login'])->name('login.oficina');
Route::post('/login/cliente', [ClienteAuthController::class, 'login'])->name('login.cliente');
Route::post('/login/cliente/selecionar', [ClienteAuthController::class, 'selecionarOficina'])->name('login.cliente.selecionar');
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
Route::post('/cliente/logout', function () {
    session()->forget(['cliente_auth', 'cliente_id', 'cliente_nome', 'tenant_id', 'oficina_nome', 'cliente_pendente']);
    return redirect()->route('login');
})->name('cliente.logout');
