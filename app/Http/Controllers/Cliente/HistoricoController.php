<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class HistoricoController extends Controller
{
    public function index()
    {
        return redirect()->route('cliente.veiculos.index');
    }
}
