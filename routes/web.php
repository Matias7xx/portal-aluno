<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Servidor\CursoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'checkRole:servidor'])->group(function () {
    // Rota base para o Dashboard do Servidor
    Route::get('/servidor', function () {
        return Inertia::render('Servidor/Home', [
            'route' => 'home'
        ]);
    })->name('servidor.home');
    
    // Rotas para gerenciamento de cursos
    Route::get('/servidor/calendario', [CursoController::class, 'calendario'])->name('servidor.calendario');
    Route::get('/servidor/curso/{curso}', [CursoController::class, 'detalhes'])->name('servidor.curso.detalhes');
    Route::get('/servidor/curso/{curso}/inscricao', [CursoController::class, 'formulario'])->name('servidor.curso.formulario');
    Route::post('/servidor/curso/inscrever/{curso}', [CursoController::class, 'inscrever'])->name('servidor.curso.inscrever');
    Route::get('/servidor/historico', [CursoController::class, 'historico'])->name('servidor.historico');
});

Route::middleware(['auth', 'checkRole:aluno'])->group(function () {
    Route::get('/aluno', function () {
        return Inertia::render('Aluno/Home');
    })->name('aluno.home');
});

require __DIR__.'/auth.php';