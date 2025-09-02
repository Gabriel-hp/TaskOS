<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\OrdemServico;
use App\Models\User;
use Carbon\Carbon;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $eventos = Evento::with('responsavel')->get();
        $users = User::all();

        $eventoId = $request->query('evento_id');
        $evento = null;

        if ($eventoId) {
            $evento = Evento::find($eventoId);
        }

        return view('agenda.index', compact('eventos', 'users', 'evento'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assunto' => 'required|string',
            'endereco' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'responsavel_id' => 'required|exists:users,id',
        ]);

        $start = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['time']);

        $evento = Evento::create([
            'title' => $validated['title'],
            'assunto' => $validated['assunto'],
            'endereco' => $validated['endereco'],
            'start' => $start,
            'responsavel_id' => $validated['responsavel_id'],
        ]);

        // Criar automaticamente uma O.S. para o evento
        OrdemServico::create([
            'evento_id' => $evento->id,
            'cliente' => $validated['title'],
            'endereco_cliente' => $validated['endereco'],
            'designacao' => $validated['assunto'],
            'hora_inicio' => $validated['time'],
            'descricao_servico' => 'Ativação',
        ]);

        return response()->json(['success' => true, 'evento' => $evento->load('responsavel')]);
    }

    public function updateStatus(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'status' => 'required|in:agendado,feito,reagendado,cancelado',
        ]);

        $evento->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }

    public function getEvents()
    {
        $eventos = Evento::with('responsavel')->get()->map(function($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->title,
                'start' => $evento->start->toISOString(),
                'backgroundColor' => $this->getStatusColor($evento->status),
                'borderColor' => $this->getStatusColor($evento->status),
                'extendedProps' => [
                    'status' => $evento->status,
                    'responsavel' => $evento->responsavel->name,
                ]
            ];
        });

        return response()->json($eventos);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'agendado' => '#3b82f6',
            'feito' => '#10b981',
            'reagendado' => '#f59e0b',
            'cancelado' => '#ef4444',
            default => '#6b7280'
        };
    }
}
