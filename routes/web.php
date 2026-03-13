<?php

use App\Livewire\Funcionario\ListarFuncionarios;
use Illuminate\Support\Facades\Route;

// Substituímos a view 'welcome' pelo componente direto
Route::get('/', ListarFuncionarios::class)->name('funcionarios.index');