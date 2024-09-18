<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [
            'post_id' => 'required|integer', // ID do post � obrigat�rio e deve ser um n�mero
            'user_id' => 'required|integer', // ID do usu�rio � obrigat�rio e deve ser um n�mero
            'comment' => 'nullable|string', // Coment�rio � opcional, mas deve ser texto
            'gif_url' => 'nullable|url', // URL do GIF � opcional, mas se presente, deve ser uma URL v�lida
            'parent_comment_id' => 'nullable|integer', // ID do coment�rio respondido, opcional
            'is_reply' => 'required|boolean', // Indica se � uma resposta (true ou false)
        ];
    }

    /**
     * Mensagens de erro personalizadas para os casos de validação.
     */
    public function messages(): array
    {
        return [
            'post_id.required' => 'O campo ID do post é  obrigatrio.',
            'user_id.required' => 'O campo ID do usuário é  obrigatrio.',
            'comment.string' => 'O comentário deve ser um texto.',
        ];
    }
}
