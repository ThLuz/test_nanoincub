<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimentacaoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo'      => 'required|in:entrada,saida',
            'valor'     => 'required|numeric|min:0.01',
            'descricao' => 'nullable|string|max:255',
        ];
    }
}
