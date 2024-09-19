<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function authorize()
    {
        // Autorizar se o usuário estiver autenticado
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'situacao_id' => 'required|integer',
            'active' => 'required|integer',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
            'about_me' => 'nullable|string',
            'website' => 'nullable|url',
            'instagram' => 'nullable|url',
            'github' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'youtube' => 'nullable|url',
            'reddit' => 'nullable|url',
            'others' => 'nullable|string',
            'followers_count' => 'nullable|integer|min:0',
            'following_count' => 'nullable|integer|min:0',
            'badge' => 'nullable|in:beginner,intermediate,superstar',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'O ID do usuário é obrigatório para requisições de atualização. verifique os parametros desta rota novamente.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'situacao_id.required' => 'A situação é obrigatória.',
            'situacao_id.integer' => 'A situação deve ser um número inteiro.',
            'active.required' => 'O status é obrigatório.',
            'active.integer' => 'O status deve ser um número inteiro.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço de email válido.',
            'email.unique' => 'O email já está em uso.',
            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser nos formatos jpeg, png, jpg, ou gif.',
            'photo.max' => 'A imagem não pode ter mais de 2048 kilobytes.',
            'location.string' => 'A localização deve ser uma string.',
            'location.max' => 'A localização não pode ter mais de 255 caracteres.',
            'website.url' => 'O link do site deve ser uma URL válida.',
            'instagram.url' => 'O link do Instagram deve ser uma URL válida.',
            'github.url' => 'O link do GitHub deve ser uma URL válida.',
            'linkedin.url' => 'O link do LinkedIn deve ser uma URL válida.',
            'youtube.url' => 'O link do YouTube deve ser uma URL válida.',
            'reddit.url' => 'O link do Reddit deve ser uma URL válida.',
            'followers_count.integer' => 'O número de seguidores deve ser um número inteiro.',
            'followers_count.min' => 'O número de seguidores não pode ser negativo.',
            'following_count.integer' => 'O número de pessoas que você está seguindo deve ser um número inteiro.',
            'following_count.min' => 'O número de pessoas que você está seguindo não pode ser negativo.',
            'badge.in' => 'A insígnia deve ser um dos seguintes valores: beginner, intermediate, superstar.',
        ];
    }
}
