<?php

namespace App\Livewire\Funcionario;

use App\Models\Funcionario;
use Livewire\Component;
use Livewire\WithPagination;

class ListarFuncionarios extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() 
    { 
        $this->resetPage(); 
    }

    public function render()
    {
        return view('livewire.funcionario.listar-funcionarios', [
            'funcionarios' => \App\Models\Funcionario::where('nome', 'like', '%' . $this->search . '%')->paginate(5)
        ]);
    }
}