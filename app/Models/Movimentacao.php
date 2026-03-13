<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes';

    const UPDATED_AT = null;

    protected $fillable = [
        'funcionario_id',
        'tipo',
        'valor',
        'descricao', 
        'created_at'        
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}