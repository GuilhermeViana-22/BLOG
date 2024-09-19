<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function generateText($prompt)
    {
        $response = $this->client->post('completions', [
            'json' => [
                'model'  => 'text-davinci-003', // Ou outro modelo suportado
                'prompt' => $prompt,
                'max_tokens' => 150,            // Máximo de tokens no resultado
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        return $body['choices'][0]['text'] ?? '';
    }
}
