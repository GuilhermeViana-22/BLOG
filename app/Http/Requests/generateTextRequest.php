<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class generateTextRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'prompt.required' => 'Insira um prompt válido para que o assistente de inteligência artificial possa te ajudar.',
        ];
    }
}
