<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'comment' => 'required|string',
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'published' => 'required|date',
        ];
    }


    /**
     * Obtenha as mensagens de validação personalizadas.
     *
     * @return array<string, string>
     *
     */public function messages(): array
{
    return [
        'title.required' => 'O título é obrigatório.',
        'title.string' => 'O título deve ser uma string.',
        'title.max' => 'O título não pode ter mais de 255 caracteres.',

        'comment.required' => 'O comentário é obrigatório.',
        'comment.string' => 'O comentário deve ser uma string.',

        'user_id.required' => 'O ID do usuário é obrigatório.',
        'user_id.integer' => 'O ID do usuário deve ser um número inteiro.',

        'category_id.required' => 'O ID da categoria é obrigatório.',
        'category_id.integer' => 'O ID da categoria deve ser um número inteiro.',

        'published.required' => 'A data de publicação é obrigatória.',
        'published.date' => 'A data de publicação deve ser uma data válida.',
    ];
}
}
