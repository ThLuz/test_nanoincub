<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Administrador extends Authenticatable
{
    use HasApiTokens, HasFactory; 

    protected $table = 'administradores';
    //public $timestamps = false;

    protected $fillable = ['nome', 'login', 'senha'];
    protected $hidden = ['senha'];

    public function getAuthPassword()
    {
        return $this->senha;
    }
}