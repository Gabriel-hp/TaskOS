@extends('layouts.app')

@section('title', 'Criar Ordem de Serviço')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-clipboard-list me-2"></i>Criar Ordem de Serviço</h4>
            </div>
            <div class="card-body">
                <!-- Informações do Evento -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Evento Relacionado</h6>
                    <p class="mb-1"><strong>Título:</strong> {{ $evento->titulo }}</p>
                    <p class="mb-1"><strong>Data/Hora:</strong> {{ $evento->data_hora->format('d/m/Y H:i') }}</p>
                    <p class="mb-0"><strong>Responsável:</strong> {{ $evento->responsavel->nome_completo }}</p>
                </div>

                <form action="{{ route('os.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cliente_tipo" class="form-label">Tipo de Cliente</label>
                                <select class="form-select" id="cliente_tipo" onchange="toggleCliente()">
                                    <option value="existente">Cliente Existente</option>
                                    <option value="novo">Novo Cliente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="cliente_existente" class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" name="cliente_id" id="cliente_id">
                            <option value="">Selecione um cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" data-endereco="{{ $cliente->endereco }}">
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="cliente_novo" class="mb-3" style="display: none;">
                        <label for="cliente_nome" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" name="cliente_nome" id="cliente_nome">
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço *</label>
                        <textarea class="form-control" name="endereco" id="endereco" rows="3" required>{{ $evento->endereco }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="designacao" class="form-label">Designação *</label>
                        <textarea class="form-control" name="designacao" id="designacao" rows="4" required placeholder="Descreva o serviço a ser realizado..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Motivo (Preenchido automaticamente)</label>
                                <textarea class="form-control" readonly>{{ $evento->assunto }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Responsável</label>
                                <input type="text" class="form-control" value="{{ $evento->responsavel->nome_completo }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data/Hora de Início</label>
                        <input type="text" class="form-control" value="{{ $evento->data_hora->format('d/m/Y H:i') }}" readonly>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('agenda') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Criar O.S.
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCliente() {
    const tipo = document.getElementById('cliente_tipo').value;
    const existente = document.getElementById('cliente_existente');
    const novo = document.getElementById('cliente_novo');
    
    if (tipo === 'existente') {
        existente.style.display = 'block';
        novo.style.display = 'none';
        document.getElementById('cliente_nome').required = false;
    } else {
        existente.style.display = 'none';
        novo.style.display = 'block';
        document.getElementById('cliente_nome').required = true;
        document.getElementById('cliente_id').value = '';
    }
}

// Auto-preencher endereço quando selecionar cliente
document.getElementById('cliente_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const endereco = selectedOption.getAttribute('data-endereco');
    
    if (endereco) {
        document.getElementById('endereco').value = endereco;
    }
});
</script>
@endpush
