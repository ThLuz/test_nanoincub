<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que qualquer usuário envie este request
    }

    public function rules()
    {
        return [
            'login' => 'required|string|max:255',
            'senha' => 'required|string|min:6',
        ];
    }
}
