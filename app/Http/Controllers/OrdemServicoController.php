<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdemServico;
use App\Models\Evento;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdemServicoController extends Controller
{
    public function show(Evento $evento)
    {
        $ordemServico = $evento->ordemServico;
        
        if (!$ordemServico) {
            $ordemServico = OrdemServico::create([
                'evento_id' => $evento->id,
                'cliente' => $evento->title,
                'endereco_cliente' => $evento->endereco,
                'designacao' => $evento->assunto,
                'hora_inicio' => $evento->start->format('H:i'),
                'descricao_servico' => 'Ativação',
                'protocolo' => $evento->protocolo,

            ]);
        }

        return response()->json([
            'evento' => $evento->load('responsavel'),
            'ordemServico' => $ordemServico
        ]);
        
    }

    public function update(Request $request, OrdemServico $ordemServico)
    {
        $validated = $request->validate([
            'cliente' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'endereco_cliente' => 'nullable|string|max:255',
            'contato_cliente' => 'nullable|string|max:255',
            'designacao' => 'nullable|string|max:255',
            'hora_inicio' => 'nullable',
            'hora_fim' => 'nullable',
            'modelo_equipamento' => 'nullable|string|max:255',
            'numero_equipamento' => 'nullable|string|max:255',
            'serial_equipamento' => 'nullable|string|max:255',
            'descricao_servico' => 'nullable|string',
            'observacao' => 'nullable|string',
            'protocolo' => 'nullable|string|max:255',

        ]);

        $ordemServico->update($validated);

        return response()->json(['success' => true]);
    }

    public function generatePDF(OrdemServico $ordemServico)
    {
        $evento = $ordemServico->evento;
        $responsavel = $evento->responsavel;
        
        $protocoloNum = $ordemServico->protocolo ?? 'SEM-PROTOCOLO';
        $pdf = Pdf::loadView('pdf.ordem-servico', compact('ordemServico', 'evento', 'responsavel', 'protocoloNum'));

        
        $fileName = "OS_{$protocoloNum}_" . now()->format('Y-m-d') . ".pdf";
        
        return $pdf->download($fileName);
    }
}
