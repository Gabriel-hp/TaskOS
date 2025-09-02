<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\UserController;


// Rotas públicas
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Agenda
    Route::get('/agenda', [EventoController::class, 'index'])->name('agenda.index');
    Route::get('/eventos/data', [EventoController::class, 'getEvents'])->name('eventos.data');
    Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
    Route::patch('/eventos/{evento}/status', [EventoController::class, 'updateStatus'])->name('eventos.updateStatus');
    
    // Ordens de Serviço
    Route::get('/ordens-servico/{evento}', [OrdemServicoController::class, 'show'])->name('ordens-servico.show');
    Route::put('/ordens-servico/{ordemServico}', [OrdemServicoController::class, 'update'])->name('ordens-servico.update');
    Route::get('/ordens-servico/{ordemServico}/pdf', [OrdemServicoController::class, 'generatePDF'])->name('ordens-servico.pdf');
    
    // Usuários 
    Route::resource('users', UserController::class);
});
