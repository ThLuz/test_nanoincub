<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FuncionarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome'  => 'required|string|max:255',
            'login' => 'required|string|max:255',
            'senha' => 'required|string|min:6',
        ];
    }
}
