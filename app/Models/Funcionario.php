<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Mude de Model para Authenticatable
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // Adicione isso

class Funcionario extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'funcionarios';
    public $timestamps = false; // Como vimos, você não usa timestamps aqui

    protected $fillable = [
        'nome',
        'login',
        'senha',
        'saldo',
        'is_admin' // Certifique-se de que este campo existe para filtrar o login
    ];

    protected $hidden = ['senha'];

    // O Laravel precisa saber que a senha não se chama 'password'
    public function getAuthPassword()
    {
        return $this->senha;
    }
}