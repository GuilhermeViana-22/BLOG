<?php

namespace App\Http\Requests;

use App\Interfaces\AppRequestInterface;
use App\Models\Token;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MeRequest extends FormRequest implements AppRequestInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required'
        ];
    }

    public function messages()
    {
        return [
                'id.required' => 'Não foi possivel localizar o usuário solicitado.'
        ];
    }

    public function getAuthorizationHeader(): ?string
    {
        // Obtém o user_id validado
        $user_id = $this->validated()['user_id'];

        // Recupera o token do banco de dados
        $tokenRecord = Token::where('user_id', $user_id)->first();

        return $tokenRecord ? 'Bearer ' . $tokenRecord->token : null;
    }
}
