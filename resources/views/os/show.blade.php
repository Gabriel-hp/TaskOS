@extends('layouts.app')

@section('title', 'Ordem de Serviço #' . $os->protocolo)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-clipboard-list me-2"></i>O.S. #{{ $os->protocolo }}</h4>
                <span class="badge bg-{{ $os->status == 'Finalizado' ? 'success' : ($os->status == 'Em execução' ? 'info' : 'warning') }} fs-6">
                    {{ $os->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i>Cliente</h6>
                        <p>{{ $os->cliente ? $os->cliente->nome : $os->cliente_nome }}</p>
                        
                        <h6><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                        <p>{{ $os->endereco }}</p>
                        
                        <h6><i class="fas fa-tasks me-2"></i>Designação</h6>
                        <p>{{ $os->designacao }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-user-tie me-2"></i>Responsável</h6>
                        <p>{{ $os->responsavel->nome_completo }}</p>
                        
                        <h6><i class="fas fa-calendar me-2"></i>Data/Hora Início</h6>
                        <p>{{ $os->data_hora_inicio->format('d/m/Y H:i') }}</p>
                        
                        <h6><i class="fas fa-comment me-2"></i>Motivo</h6>
                        <p>{{ $os->motivo }}</p>
                    </div>
                </div>

                @if($os->observacao)
                <div class="mt-4">
                    <h6><i class="fas fa-sticky-note me-2"></i>Observações</h6>
                    <p>{{ $os->observacao }}</p>
                </div>
                @endif

                @if($os->anexo)
                <div class="mt-4">
                    <h6><i class="fas fa-paperclip me-2"></i>Anexo</h6>
                    <a href="{{ Storage::url($os->anexo) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Baixar Anexo
                    </a>
                </div>
                @endif

                @if($os->status != 'Finalizado')
                <div class="mt-4">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finalizarModal">
                        <i class="fas fa-check me-2"></i>Marcar como Resolvido
                    </button>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('agenda') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar para Agenda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Finalizar O.S. -->
<div class="modal fade" id="finalizarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finalizar Ordem de Serviço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('os.finalizar', $os->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="anexo" class="form-label">Anexar O.S. Assinada</label>
                        <input type="file" class="form-control" name="anexo" id="anexo" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Formatos aceitos: PDF, JPG, PNG</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observações do Técnico</label>
                        <textarea class="form-control" name="observacao" id="observacao" rows="4" placeholder="Descreva o que foi realizado, materiais utilizados, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Finalizar O.S.
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
