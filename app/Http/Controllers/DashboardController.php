<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\OrdemServico;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        $eventos = Evento::with('responsavel')->get();
        $ordensServico = OrdemServico::with(['responsavel', 'evento'])->get();
        
        return view('dashboard.index', compact('usuarios', 'eventos', 'ordensServico'));
    }
}
