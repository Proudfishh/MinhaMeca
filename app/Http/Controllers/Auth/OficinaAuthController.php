<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OficinaAuthController extends Controller
{
    public function login(Request $request)
    {
        session([
            'oficina_auth' => true,
            'oficina_nome' => 'Auto Center Premium',
            'tenant_id'    => 1,
        ]);

        return redirect()->route('oficina.dashboard');
    }
}
