<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.required'   => 'O e-mail é obrigatório.',
            'email.email'      => 'O e-mail deve ser um endereço de e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min'     => 'A senha deve ter no mínimo 8 caracteres.',
        ];
    }
}
