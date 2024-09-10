<?php
namespace App\Http\Requests\Posts;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255', // Se o subtítulo não for obrigatório
            'body' => 'required|string',
            'footer' => 'nullable|string',
            'links' => 'nullable|string',
            'tags_id' => 'required|string', // Se for uma lista de IDs separados por vírgulas
            'image_url' => 'nullable|url', // Valida a URL da imagem
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'type_id' => 'required|integer', // Supondo que tenha uma tabela types
            'can_be_commented' => 'nullable|boolean',
            'created_at' => 'nullable|date', // Se não precisar validar, pode remover
            'updated_at' => 'nullable|date', // Se não precisar validar, pode remover
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
            'id.required' => 'O id é obrigatório, para realiza-se alguma edição.',
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',

            'subtitle.string' => 'O subtítulo deve ser uma string.',
            'subtitle.max' => 'O subtítulo não pode ter mais de 255 caracteres.',

            'body.required' => 'O corpo do post é obrigatório.',
            'body.string' => 'O corpo do post deve ser uma string.',

            'footer.string' => 'O rodapé deve ser uma string.',

            'links.string' => 'Os links devem ser uma string.',

            'tags_id.string' => 'Os IDs das tags devem ser uma string.',


            'image_url.url' => 'A URL da imagem deve ser uma URL válida.',

            'user_id.required' => 'O ID do usuário é obrigatório.',


            'category_id.required' => 'O ID da categoria é obrigatório.',
            'category_id.integer' => 'O ID da categoria deve ser um número inteiro.',





        ];
    }
}
