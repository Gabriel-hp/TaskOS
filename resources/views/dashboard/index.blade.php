@extends('layouts.app')

@section('title', 'Dashboard - TaskOS')

@section('content')
<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs mb-4" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab">
                    <i class="fas fa-calendar-alt me-2"></i>Agenda
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>Eventos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="os-tab" data-bs-toggle="tab" data-bs-target="#os" type="button" role="tab">
                    <i class="fas fa-clipboard-list me-2"></i>O.S.
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Usuários
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>Relatórios
                </button>
            </li>
        </ul>

        <div class="tab-content" id="mainTabsContent">
            <!-- Aba Agenda -->
            <div class="tab-pane fade show active" id="calendar" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-2" id="prevMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h2 id="currentMonth" class="mb-0 mx-3"></h2>
                        <button class="btn btn-outline-secondary ms-2" id="nextMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary me-2" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Evento
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <!-- Cabeçalho dos dias da semana -->
                        <div class="calendar-grid" style="grid-template-rows: auto;">
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Dom</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Seg</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Ter</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Qua</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Qui</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Sex</div>
                            <div class="calendar-day text-center fw-bold py-2" style="min-height: auto;">Sáb</div>
                        </div>
                        <!-- Grid do calendário -->
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Dias serão inseridos via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba Eventos -->
            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Lista de Eventos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="searchEvents" placeholder="Buscar eventos...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterEventStatus">
                                    <option value="">Todos os status</option>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Em execução">Em execução</option>
                                    <option value="Finalizado">Finalizado</option>
                                    <option value="Reagendado">Reagendado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterEventResponsavel">
                                    <option value="">Todos os responsáveis</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="eventsList">
                            <!-- Lista será carregada via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba O.S. -->
            <div class="tab-pane fade" id="os" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ordens de Serviço</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="searchOS" placeholder="Buscar O.S...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterOSStatus">
                                    <option value="">Todos os status</option>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Em execução">Em execução</option>
                                    <option value="Finalizado">Finalizado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterOSResponsavel">
                                    <option value="">Todos os responsáveis</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="osList">
                            <!-- Lista será carregada via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba Usuários -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Gerenciar Usuários</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Usuário
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="searchUsers" placeholder="Buscar usuários...">
                            </div>
                        </div>
                        <div id="usersList">
                            <!-- Lista será carregada via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba Relatórios -->
            <div class="tab-pane fade" id="reports" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Relatórios</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" id="reportDateFrom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data Final</label>
                                <input type="date" class="form-control" id="reportDateTo">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Responsável</label>
                                <select class="form-select" id="reportResponsavel">
                                    <option value="">Todos</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-success w-100" onclick="generateReport()">
                                    <i class="fas fa-file-pdf me-2"></i>Gerar Relatório
                                </button>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total de Eventos</h6>
                                                <h3 id="totalEventos">{{ $eventos->count() }}</h3>
                                            </div>
                                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total de O.S.</h6>
                                                <h3 id="totalOS">{{ $ordensServico->count() }}</h3>
                                            </div>
                                            <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Taxa de Conclusão</h6>
                                                <h3 id="taxaConclusao">
                                                    @php
                                                        $finalizados = $eventos->where('status', 'Finalizado')->count();
                                                        $total = $eventos->count();
                                                        $taxa = $total > 0 ? round(($finalizados / $total) * 100) : 0;
                                                    @endphp
                                                    {{ $taxa }}%
                                                </h3>
                                            </div>
                                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="reportContent">
                            <!-- Conteúdo do relatório será carregado aqui -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Evento -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventTitulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="eventTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventAssunto" class="form-label">Assunto *</label>
                        <textarea class="form-control" id="eventAssunto" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="eventEndereco" class="form-label">Endereço *</label>
                        <textarea class="form-control" id="eventEndereco" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventData" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="eventData" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventHora" class="form-label">Hora *</label>
                                <input type="time" class="form-control" id="eventHora" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventResponsavel" class="form-label">Responsável *</label>
                        <select class="form-select" id="eventResponsavel" required>
                            <option value="">Selecione...</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->nome_completo }}</option>
                            @endforeach
                        </select>
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

<!-- Modal Adicionar Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userNome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="userNome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userSobrenome" class="form-label">Sobrenome *</label>
                                <input type="text" class="form-control" id="userSobrenome" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="userEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="userSenha" class="form-label">Senha *</label>
                        <input type="password" class="form-control" id="userSenha" required>
                    </div>
                    <div class="alert alert-info">
                        <small>O username será gerado automaticamente no formato: nome.sobrenome</small>
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
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar O.S. -->
<div class="modal fade" id="createOSModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Criar Ordem de Serviço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createOSForm">
                <div class="modal-body">
                    <input type="hidden" id="osEventoId">
                    <div class="mb-3">
                        <label for="osProtocolo" class="form-label">Protocolo *</label>
                        <input type="text" class="form-control" id="osProtocolo" placeholder="Ex: OS-2024-001" required>
                        <small class="text-muted">Digite o protocolo do sistema externo</small>
                    </div>
                    <div class="mb-3">
                        <label for="osClienteNome" class="form-label">Nome do Cliente *</label>
                        <input type="text" class="form-control" id="osClienteNome" placeholder="Digite o nome do cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="osEndereco" class="form-label">Endereço *</label>
                        <textarea class="form-control" id="osEndereco" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="osDesignacao" class="form-label">Designação *</label>
                        <input type="text" class="form-control" id="osDesignacao" placeholder="Ex: Técnico de campo, Engenheiro, etc." required>
                    </div>
                    <div class="mb-3">
                        <label for="osMotivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="osMotivo" rows="3" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="osObservacao" class="form-label">Observações</label>
                        <textarea class="form-control" id="osObservacao" rows="2" placeholder="Observações adicionais (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar O.S.</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalhes O.S. -->
<div class="modal fade" id="osDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da O.S.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="osDetailsContent">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
