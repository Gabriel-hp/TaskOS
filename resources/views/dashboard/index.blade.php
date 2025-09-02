@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Eventos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Eventos Concluídos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['feitos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Agendados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['agendados'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Este Mês
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['thisMonth'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-month fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>
                </h6>
            </div>
            <div class="card-body">
                @foreach($userStats as $stat)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                    <div>
                        <h6 class="mb-1">{{ $stat['user']->name }}</h6>
                        <small class="text-muted">{{ $stat['totalEvents'] }} evento(s) total</small>
                    </div>
                    <div class="text-end">
                        <div class="h5 mb-0 text-success">{{ $stat['completedEvents'] }}</div>
                        <small class="text-muted">{{ $stat['completionRate'] }}% concluídos</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
 
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                </h6>
            </div>
            
            <div class="card-body">
                @forelse($recentEvents as $event)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                        <a href="/agenda?evento={{ $event->id }}">    
                            <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $event->title }}</h6>
                            <small class="text-muted">
                                {{ $event->responsavel->name }} • 
                                {{ $event->start->format('d/m/Y H:i') }}
                            </small>
                        </a>
                    </div>
                    <span class="badge status-badge 
                        @if($event->status === 'agendado') bg-primary
                        @elseif($event->status === 'feito') bg-success
                        @elseif($event->status === 'reagendado') bg-warning
                        @else bg-danger
                        @endif">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>Nenhum evento criado ainda</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection
