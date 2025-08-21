<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdemServico;
use App\Models\Evento;
use Illuminate\Support\Facades\Storage;

class OrdemServicoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdemServico::with(['evento', 'responsavel']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('protocolo', 'like', "%{$search}%")
                  ->orWhere('cliente_nome', 'like', "%{$search}%")
                  ->orWhere('designacao', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('responsavel_id')) {
            $query->where('responsavel_id', $request->responsavel_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_hora_inicio', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_hora_inicio', '<=', $request->data_fim);
        }

        $ordens = $query->orderBy('created_at', 'desc')->get();

        if ($request->expectsJson()) {
            return response()->json($ordens);
        }

        return view('os.index', compact('ordens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'protocolo' => 'required|string|max:255',
            'cliente_nome' => 'required|string|max:255',
            'endereco' => 'required|string',
            'designacao' => 'required|string|max:255',
            'observacao' => 'nullable|string',
        ]);

        $evento = Evento::findOrFail($request->evento_id);

        $os = OrdemServico::create([
            'evento_id' => $request->evento_id,
            'protocolo' => $request->protocolo,
            'cliente_nome' => $request->cliente_nome,
            'endereco' => $request->endereco,
            'designacao' => $request->designacao,
            'motivo' => $evento->assunto,
            'data_hora_inicio' => $evento->data_hora,
            'responsavel_id' => $evento->responsavel_id,
            'observacao' => $request->observacao,
            'status' => 'Pendente',
        ]);

        return response()->json([
            'success' => true,
            'os' => $os->load(['responsavel', 'evento'])
        ]);
    }

    public function show($id)
    {
        $os = OrdemServico::with(['evento', 'responsavel'])->findOrFail($id);
        return response()->json($os);
    }

    public function update(Request $request, $id)
    {
        $os = OrdemServico::findOrFail($id);

        $request->validate([
            'status' => 'sometimes|required|in:Pendente,Em execução,Finalizado',
            'observacao' => 'nullable|string',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->only(['status', 'observacao']);

        if ($request->hasFile('anexo')) {
            // Remove anexo anterior se existir
            if ($os->anexo && Storage::disk('public')->exists($os->anexo)) {
                Storage::disk('public')->delete($os->anexo);
            }

            $arquivo = $request->file('anexo');
            $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
            $caminho = $arquivo->storeAs('os_anexos', $nomeArquivo, 'public');
            $data['anexo'] = $caminho;
        }

        $os->update($data);

        // Atualizar status do evento se a O.S. foi finalizada
        if ($request->status === 'Finalizado') {
            $os->evento->update(['status' => 'Finalizado']);
        }

        return response()->json([
            'success' => true,
            'os' => $os->load(['responsavel', 'evento'])
        ]);
    }

    public function print($id)
    {
        $os = OrdemServico::with(['evento', 'responsavel'])->findOrFail($id);
        return view('os.print', compact('os'));
    }
}
