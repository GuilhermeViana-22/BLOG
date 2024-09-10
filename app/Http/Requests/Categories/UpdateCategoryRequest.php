<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{

    /**
     * Regras de validação para a requisição de atualização da categoria.
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'user_id' => 'required',
            'name' => 'required',
            'title' => 'required',
            'description' => 'nullable|string',
            'relevant' => 'nullable|boolean',
            'published' => 'nullable|date',
            'parent_id' => 'nullable',
        ];
    }

    /**
     * Mensagens de erro personalizadas para os casos de validação.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'O id dessa categoria deve ser informado.',
            'user_id.required' => 'O user_id dessa alteração deve ser informado para que o sistema registre o log das alterações.',
            'name.required' => 'O campo categoria é obrigatório.',
            'title.required' => 'O campo "título" é obrigatório.',

        ];
    }
}
