@extends('layouts.app')

@section('title', 'Agenda Compartilhada')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt me-2"></i>Agenda Compartilhada</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventoModal">
                <i class="fas fa-plus me-2"></i>Adicionar Evento
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar/Editar Evento -->
<div class="modal fade" id="eventoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="eventoForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="evento_id" name="evento_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="responsavel_id" class="form-label">Responsável *</label>
                                <select class="form-select" id="responsavel_id" name="responsavel_id" required>
                                    <option value="">Selecione...</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto *</label>
                        <textarea class="form-control" id="assunto" name="assunto" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço *</label>
                        <textarea class="form-control" id="endereco" name="endereco" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="data" name="data" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora *</label>
                                <input type="time" class="form-control" id="hora" name="hora" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalhes do Evento -->
<div class="modal fade" id="detalhesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesContent">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" id="editarEvento">
                    <i class="fas fa-edit me-2"></i>Editar
                </button>
                <button type="button" class="btn btn-success" id="criarOS" style="display: none;">
                    <i class="fas fa-clipboard-list me-2"></i>Fazer O.S.
                </button>
                <button type="button" class="btn btn-danger" id="excluirEvento">
                    <i class="fas fa-trash me-2"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    let eventoAtual = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route("agenda.eventos") }}',
        eventClick: function(info) {
            mostrarDetalhes(info.event.id);
        },
        selectable: true,
        select: function(info) {
            document.getElementById('data').value = info.startStr;
            document.getElementById('hora').value = '08:00';
            $('#eventoModal').modal('show');
        }
    });

    calendar.render();

    // Form de evento
    document.getElementById('eventoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        data.data_hora = data.data + ' ' + data.hora + ':00';
        
        const eventoId = document.getElementById('evento_id').value;
        const url = eventoId ? `/agenda/eventos/${eventoId}` : '{{ route("agenda.store") }}';
        const method = eventoId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#eventoModal').modal('hide');
                calendar.refetchEvents();
                this.reset();
                document.getElementById('evento_id').value = '';
            }
        });
    });

    function mostrarDetalhes(eventoId) {
        fetch(`/agenda/eventos/${eventoId}`)
        .then(response => response.json())
        .then(evento => {
            eventoAtual = evento;
            
            const statusBadge = getStatusBadge(evento.status);
            const dataFormatada = new Date(evento.data_hora).toLocaleString('pt-BR');
            
            document.getElementById('detalhesContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-tag me-2"></i>Título</h6>
                        <p>${evento.titulo}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i>Responsável</h6>
                        <p>${evento.responsavel.nome_completo}</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-comment me-2"></i>Assunto</h6>
                    <p>${evento.assunto}</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                    <p>${evento.endereco}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar me-2"></i>Data/Hora</h6>
                        <p>${dataFormatada}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle me-2"></i>Status</h6>
                        <p>${statusBadge}</p>
                    </div>
                </div>
            `;
            
            // Mostrar/ocultar botão de criar O.S.
            const criarOSBtn = document.getElementById('criarOS');
            if (evento.ordem_servico) {
                criarOSBtn.style.display = 'none';
            } else {
                criarOSBtn.style.display = 'inline-block';
                criarOSBtn.onclick = () => {
                    window.location.href = `/os/criar/${evento.id}`;
                };
            }
            
            $('#detalhesModal').modal('show');
        });
    }

    function getStatusBadge(status) {
        const badges = {
            'Pendente': '<span class="badge bg-warning">Pendente</span>',
            'Em execução': '<span class="badge bg-info">Em execução</span>',
            'Finalizado': '<span class="badge bg-success">Finalizado</span>',
            'Reagendado': '<span class="badge bg-danger">Reagendado</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Indefinido</span>';
    }

    // Botão editar
    document.getElementById('editarEvento').addEventListener('click', function() {
        if (eventoAtual) {
            document.getElementById('evento_id').value = eventoAtual.id;
            document.getElementById('titulo').value = eventoAtual.titulo;
            document.getElementById('assunto').value = eventoAtual.assunto;
            document.getElementById('endereco').value = eventoAtual.endereco;
            document.getElementById('responsavel_id').value = eventoAtual.responsavel_id;
            
            const dataHora = new Date(eventoAtual.data_hora);
            document.getElementById('data').value = dataHora.toISOString().split('T')[0];
            document.getElementById('hora').value = dataHora.toTimeString().slice(0, 5);
            
            $('#detalhesModal').modal('hide');
            $('#eventoModal').modal('show');
        }
    });

    // Botão excluir
    document.getElementById('excluirEvento').addEventListener('click', function() {
        if (eventoAtual && confirm('Tem certeza que deseja excluir este evento?')) {
            fetch(`/agenda/eventos/${eventoAtual.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#detalhesModal').modal('hide');
                    calendar.refetchEvents();
                }
            });
        }
    });
});
</script>
@endpush
