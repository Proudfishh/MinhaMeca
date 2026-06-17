<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OficinaAuthController;
use App\Http\Controllers\Auth\ClienteAuthController;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', fn () => view('auth.login'))->name('login');

Route::post('/login/oficina', [OficinaAuthController::class, 'login'])->name('login.oficina');
Route::post('/login/cliente', [ClienteAuthController::class, 'login'])->name('login.cliente');
Route::post('/logout', fn () => redirect()->route('login'))->name('logout');
