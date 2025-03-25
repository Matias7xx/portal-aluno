<?php

use App\Http\Controllers\ProfileController;
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
    
    // Rota para o Calendário de Cursos dentro do Dashboard
    Route::get('/servidor/calendario', function () {
        return Inertia::render('Servidor/Home', [
            'route' => 'calendario'
        ]);
    })->name('servidor.calendario');
    
    // Adicionar futuras rotas aqui usando o mesmo padrão
    // Route::get('/servidor/historico', function () {
    //     return Inertia::render('Servidor/Home', [
    //         'route' => 'historico'
    //     ]);
    // })->name('servidor.historico');
    
    // Route::get('/servidor/sugeridos', function () {
    //     return Inertia::render('Servidor/Home', [
    //         'route' => 'sugeridos'
    //     ]);
    // })->name('servidor.sugeridos');
});

Route::middleware(['auth', 'checkRole:aluno'])->group(function () {
    Route::get('/aluno', function () {
        return Inertia::render('Aluno/Home');
    })->name('aluno.home');
});

require __DIR__.'/auth.php';