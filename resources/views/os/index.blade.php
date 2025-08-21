@extends('layouts.app')

@section('title', 'Ordens de Serviço')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-clipboard-list me-2"></i>Ordens de Serviço</h2>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" name="data_fim" id="data_fim" value="{{ request('data_fim') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="responsavel_id" class="form-label">Responsável</label>
                        <select class="form-select" name="responsavel_id" id="responsavel_id">
                            <option value="">Todos</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ request('responsavel_id') == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">Todos</option>
                            <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Em execução" {{ request('status') == 'Em execução' ? 'selected' : '' }}>Em execução</option>
                            <option value="Finalizado" {{ request('status') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('os.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de O.S. -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Cliente</th>
                                <th>Responsável</th>
                                <th>Data/Hora</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordens as $os)
                                <tr>
                                    <td>
                                        <strong>{{ $os->protocolo }}</strong>
                                    </td>
                                    <td>
                                        {{ $os->cliente ? $os->cliente->nome : $os->cliente_nome }}
                                    </td>
                                    <td>
                                        {{ $os->responsavel->nome_completo }}
                                    </td>
                                    <td>
                                        {{ $os->data_hora_inicio->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $os->status == 'Finalizado' ? 'success' : ($os->status == 'Em execução' ? 'info' : 'warning') }}">
                                            {{ $os->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('os.show', $os->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma ordem de serviço encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $ordens->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
