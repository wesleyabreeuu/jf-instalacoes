<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ServicoController;
use App\Http\Controllers\Admin\ServicoMaterialController;
use App\Http\Controllers\Admin\RelatorioController;

Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * Dashboard (Breeze)
 * - Admin vai para /admin
 * - Colaborador vai para /app
 */
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.home');
    }

    return redirect()->route('app.home');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Área geral logada (admin e colaborador)
 */
Route::middleware(['auth', 'verified'])->group(function () {

    /**
     * Área do colaborador /app
     */
    Route::get('/app', function () {
        return view('app.home');
    })->name('app.home');

    /**
     * Área ADMIN (prefixo /admin)
     */
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Home do admin: GET /admin
            Route::get('/', [AdminController::class, 'home'])->name('home');

            // CRUDs admin
            Route::resource('usuarios', UserController::class)->except(['show']);
            Route::resource('clientes', ClienteController::class)->except(['show']);

            Route::resource('materiais', MaterialController::class)
                ->parameters(['materiais' => 'material'])
                ->except(['show']);

            /**
             * ✅ Relatórios
             */
            Route::get('relatorios', [RelatorioController::class, 'index'])
                ->name('relatorios.index');

            Route::get('relatorios/pdf', [RelatorioController::class, 'pdf'])
                ->name('relatorios.pdf');

            /**
             * ✅ Serviços
             * - Tem SHOW (tela de exibição com botões de status)
             * - NÃO tem EDIT/UPDATE
             */
            Route::resource('servicos', ServicoController::class)->except(['edit', 'update']);

            // ✅ PDF do Serviço (BOTÃO PDF)
            Route::get('servicos/{servico}/pdf', [ServicoController::class, 'pdf'])
                ->name('servicos.pdf');

            // ✅ Rota para mudar status (AGENDADO->ABERTO->EM_EXECUCAO->FINALIZADO)
            Route::patch('servicos/{servico}/status', [ServicoController::class, 'updateStatus'])
                ->name('servicos.status');

            /**
             * ✅ Materiais do Serviço (lançamento de consumo)
             */
            Route::get('servicos/{servico}/materiais', [ServicoMaterialController::class, 'create'])
                ->name('servicos.materiais.create');

            Route::post('servicos/{servico}/materiais', [ServicoMaterialController::class, 'store'])
                ->name('servicos.materiais.store');
        });

    /**
     * Profile (Breeze)
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::view('/offline', 'offline')->name('offline');


require __DIR__ . '/auth.php';
