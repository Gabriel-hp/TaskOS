<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\UserController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

/*
// Rotas de autenticação
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
*/

// Rotas protegidas
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API Routes para AJAX
    Route::prefix('api')->group(function () {
        // Eventos
        Route::get('/eventos', [EventoController::class, 'index'])->name('api.eventos.index');
        Route::post('/eventos', [EventoController::class, 'store'])->name('api.eventos.store');
        Route::get('/eventos/{id}', [EventoController::class, 'show'])->name('api.eventos.show');
        Route::put('/eventos/{id}', [EventoController::class, 'update'])->name('api.eventos.update');
        Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])->name('api.eventos.destroy');

        // Ordens de Serviço
        Route::get('/os', [OrdemServicoController::class, 'index'])->name('api.os.index');
        Route::post('/os', [OrdemServicoController::class, 'store'])->name('api.os.store');
        Route::get('/os/{id}', [OrdemServicoController::class, 'show'])->name('api.os.show');
        Route::put('/os/{id}', [OrdemServicoController::class, 'update'])->name('api.os.update');

        // Usuários
        Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('api.users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('api.users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
    });

    // Impressão de O.S.
    Route::get('/os/{id}/print', [OrdemServicoController::class, 'print'])->name('os.print');
});


