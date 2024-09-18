<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

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
            'subtitle' => 'nullable|string|max:255',
            'body' => 'required|string',
            'footer' => 'nullable|string',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.url' => 'nullable|url',
            'tags_id' => 'nullable|array',
            'tags_id.*' => 'integer|exists:tags,id',
            'image_url' => 'nullable|url',
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'type_id' => 'required|integer',
            'can_be_commented' => 'nullable|boolean',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',

            'subtitle.string' => 'O subtítulo deve ser uma string.',
            'subtitle.max' => 'O subtítulo não pode ter mais de 255 caracteres.',

            'body.required' => 'O corpo do post é obrigatório.',
            'body.string' => 'O corpo do post deve ser uma string.',

            'footer.string' => 'O rodapé deve ser uma string.',

            'links.array' => 'Os links devem ser uma array.',
            'links.*.title.string' => 'O título do link deve ser uma string.',
            'links.*.title.max' => 'O título do link não pode ter mais de 255 caracteres.',
            'links.*.url.url' => 'A URL do link deve ser uma URL válida.',

            'tags_id.array' => 'Os IDs das tags devem ser uma array.',
            'tags_id.*.integer' => 'Cada ID de tag deve ser um número inteiro.',
            'tags_id.*.exists' => 'O ID da tag deve existir.',

            'image_url.url' => 'A URL da imagem deve ser uma URL válida.',

            'user_id.required' => 'O ID do usuário é obrigatório.',
            'user_id.integer' => 'O ID do usuário deve ser um número inteiro.',

            'category_id.required' => 'O ID da categoria é obrigatório.',
            'category_id.integer' => 'O ID da categoria deve ser um número inteiro.',

            'type_id.required' => 'O ID do tipo é obrigatório.',
            'type_id.integer' => 'O ID do tipo deve ser um número inteiro.',

            'can_be_commented.boolean' => 'O campo "pode ser comentado" deve ser um valor booleano.',
        ];
    }
}
