<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with('responsavel')->get();
        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'assunto' => 'required|string',
            'endereco' => 'required|string',
            'data_hora' => 'required|date',
            'responsavel_id' => 'required|exists:users,id',
        ]);

        $evento = Evento::create($request->all());
        $evento->load('responsavel');

        return response()->json([
            'success' => true,
            'evento' => $evento
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
        
        $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'assunto' => 'sometimes|required|string',
            'endereco' => 'sometimes|required|string',
            'data_hora' => 'sometimes|required|date',
            'responsavel_id' => 'sometimes|required|exists:users,id',
            'status' => 'sometimes|required|in:Pendente,Em execução,Finalizado,Reagendado',
        ]);

        $evento->update($request->all());
        $evento->load('responsavel');

        return response()->json([
            'success' => true,
            'evento' => $evento
        ]);
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();

        return response()->json(['success' => true]);
    }
}
