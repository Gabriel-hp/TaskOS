<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;
use App\Models\Cliente;

class AgendaController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        $clientes = Cliente::all();
        return view('agenda.index', compact('usuarios', 'clientes'));
    }

    public function getEventos()
    {
        $eventos = Evento::with('responsavel')->get();
        
        $eventosFormatados = $eventos->map(function ($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->data_hora->format('Y-m-d H:i:s'),
                'backgroundColor' => $this->getCorStatus($evento->status),
                'borderColor' => $this->getCorStatus($evento->status),
                'extendedProps' => [
                    'assunto' => $evento->assunto,
                    'endereco' => $evento->endereco,
                    'responsavel' => $evento->responsavel->nome_completo,
                    'status' => $evento->status,
                    'tem_os' => $evento->ordemServico ? true : false,
                ]
            ];
        });

        return response()->json($eventosFormatados);
    }

    private function getCorStatus($status)
    {
        return match($status) {
            'Pendente' => '#ffc107',
            'Em execução' => '#17a2b8',
            'Finalizado' => '#28a745',
            'Reagendado' => '#dc3545',
            default => '#6c757d'
        };
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required',
            'assunto' => 'required',
            'endereco' => 'required',
            'data_hora' => 'required|date',
            'responsavel_id' => 'required|exists:users,id',
        ]);

        $evento = Evento::create($request->all());

        return response()->json([
            'success' => true,
            'evento' => $evento->load('responsavel')
        ]);
    }

    public function show($id)
    {
        $evento = Evento::with(['responsavel', 'ordemServico'])->findOrFail($id);
        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        $evento->update($request->all());
        
        return response()->json([
            'success' => true,
            'evento' => $evento->load('responsavel')
        ]);
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();
        
        return response()->json(['success' => true]);
    }
}
