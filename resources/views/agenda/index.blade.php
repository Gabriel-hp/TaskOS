@extends('layouts.app')

@section('title', 'Agenda')
@section('page-title', 'Agenda')

@section('page-actions')
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#eventModal">
    <i class="fas fa-plus me-2"></i>
    Novo Evento
</button>
@endsection

<style>
    .fc-button-primary {
        color: #ffffffff !important; 
    }
    /* Texto dos dias */
.fc-daygrid-day-number {
    color: #0000008c !important;
}
.fc-col-header-cell {
    color: #000000ff !important; 
    font-weight: bold;
    background-color: #ffffffff; 
}


/* Texto do nome do mês */
.fc-toolbar-title {
    color: #191a1bff !important;
}

/* Texto dos eventos */
.fc-event-title {
    color: #191a1bff !important;
}

</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow text-dark">
            <div class="card-body text-dark">
                <div class="text-dark" id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title ">
                    <i class="fas fa-calendar-plus me-2 text-dark"></i>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="eventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto *</label>
                        <textarea class="form-control" id="assunto" name="assunto" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço *</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time" class="form-label">Horário *</label>
                                <input type="time" class="form-control" id="time" name="time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="responsavel_id" class="form-label">Responsável *</label>
                        <select class="form-select" id="responsavel_id" name="responsavel_id" required>
                            <option value="">Selecione o responsável</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Evento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="osModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="osContent">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana'
        },
        events: '{{ route("eventos.data") }}',
        dateClick: function(info) {
            $('#date').val(info.dateStr);
            $('#eventModal').modal('show');
        },
        eventClick: function(info) {
            loadOrdemServico(info.event.id);
        }
    });
    
    calendar.render();

    @if($evento)
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Preenche os campos da modal
                document.querySelector('#eventModal .modal-title').innerText = "{{ $evento->titulo }}";
                document.querySelector('#eventModal .modal-body').innerHTML = "{{ $evento->descricao }}";

                // Abre a modal
                var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
                myModal.show();
            });
        </script>
    @endif


    // Submissão do formulário de evento
    $('#eventForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("eventos.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#eventModal').modal('hide');
                calendar.refetchEvents();
                $('#eventForm')[0].reset();
                
                // Mostrar mensagem de sucesso
                $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                  'Evento criado com sucesso!' +
                  '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                  '</div>').prependTo('main').delay(3000).fadeOut();
            },
            error: function(xhr) {
                console.error('Erro ao criar evento:', xhr.responseJSON);
            }
        });
    });

    // Função para carregar ordem de serviço
    function loadOrdemServico(eventoId) {
        $.ajax({
            url: `/ordens-servico/${eventoId}`,
            method: 'GET',
            success: function(response) {
                renderOrdemServico(response);
                $('#osModal').modal('show');
            },
            error: function(xhr) {
                console.error('Erro ao carregar ordem de serviço:', xhr.responseJSON);
            }
        });
    }

    // Função para renderizar ordem de serviço
    function renderOrdemServico(data) {
        const evento = data.evento;
        const os = data.ordemServico;
        
        const statusColors = {
            'agendado': 'primary',
            'feito': 'success',
            'reagendado': 'warning',
            'cancelado': 'danger'
        };

        const html = `
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Status do Evento</h6>
                    <span class="badge bg-${statusColors[evento.status]} status-badge">
                        ${evento.status.charAt(0).toUpperCase() + evento.status.slice(1)}
                    </span>
                </div>
                
                <div class="btn-group w-100 mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary status-btn" data-status="agendado" data-evento="${evento.id}">
                        <i class="fas fa-clock me-1"></i> Agendado
                    </button>
                    <button type="button" class="btn btn-outline-success status-btn" data-status="feito" data-evento="${evento.id}">
                        <i class="fas fa-check me-1"></i> Feito
                    </button>
                    <button type="button" class="btn btn-outline-warning status-btn" data-status="reagendado" data-evento="${evento.id}">
                        <i class="fas fa-redo me-1"></i> Reagendar
                    </button>
                    <button type="button" class="btn btn-outline-danger status-btn" data-status="cancelado" data-evento="${evento.id}">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                </div>
            </div>

            <form id="osForm" data-os-id="${os.id}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Evento:</label>
                        <p class="form-control-plaintext">${evento.title}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Responsável:</label>
                        <p class="form-control-plaintext">${evento.responsavel.name}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data/Hora:</label>
                    <p class="form-control-plaintext">${new Date(evento.start).toLocaleString('pt-BR')}</p>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="protocolo" class="form-label">Protocolo</label>
                        <input type="text" class="form-control" name="protocolo" value="${os.protocolo || ''}">
                    </div>

                <h6 class="mb-3"><i class="fas fa-building me-2"></i>Dados do Cliente</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" class="form-control" name="cliente" value="${os.cliente || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control" name="cnpj" value="${os.cnpj || ''}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="endereco_cliente" class="form-label">Endereço</label>
                    <input type="text" class="form-control" name="endereco_cliente" value="${os.endereco_cliente || ''}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contato_cliente" class="form-label">Contato</label>
                        <input type="text" class="form-control" name="contato_cliente" value="${os.contato_cliente || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="designacao" class="form-label">Designação</label>
                        <input type="text" class="form-control" name="designacao" value="${os.designacao || ''}">
                    </div>
                </div>

                <hr>

                <h6 class="mb-3"><i class="fas fa-clock me-2"></i>Horários</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hora_inicio" class="form-label">Hora Início</label>
                        <input type="time" class="form-control" name="hora_inicio" value="${os.hora_inicio || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="hora_fim" class="form-label">Hora Fim</label>
                        <input type="time" class="form-control" name="hora_fim" value="${os.hora_fim || ''}">
                    </div>
                </div>

                <hr>                

                <h6 class="mb-3"><i class="fas fa-file-alt me-2"></i>Descrição do Serviço</h6>
                
                <div class="mb-3">
                    <label for="descricao_servico" class="form-label">Descrição</label>
                    <textarea class="form-control" name="descricao_servico" rows="3">${os.descricao_servico || ''}</textarea>
                </div>

                <div class="mb-3">
                    <label for="observacao" class="form-label">Observação</label>
                    <textarea class="form-control" name="observacao" rows="3">${os.observacao || ''}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="/ordens-servico/${os.id}/pdf" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> Gerar PDF
                    </a>
                </div>
            </form>
        `;
        
        $('#osContent').html(html);
    }

    // Event handlers para status e formulário OS
    $(document).on('click', '.status-btn', function() {
        const status = $(this).data('status');
        const eventoId = $(this).data('evento');
        
        $.ajax({
            url: `/eventos/${eventoId}/status`,
            method: 'PATCH',
            data: { status: status },
            success: function() {
                calendar.refetchEvents();
                $('#osModal').modal('hide');
            }
        });
    });

    $(document).on('submit', '#osForm', function(e) {
        e.preventDefault();
        const osId = $(this).data('os-id');
        
        $.ajax({
            url: `/ordens-servico/${osId}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function() {
                $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                  'Ordem de serviço salva com sucesso!' +
                  '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                  '</div>').prependTo('#osContent');
            }
        });
    });
});
</script>
@endpush
