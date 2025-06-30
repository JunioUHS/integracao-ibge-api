<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uf' => 'required|string|size:2|regex:/^[A-Z]{2}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'uf.required' => 'UF é obrigatório',
            'uf.size' => 'UF deve ter exatamente 2 caracteres',
            'uf.regex' => 'UF deve conter apenas letras maiúsculas',
        ];
    }
}