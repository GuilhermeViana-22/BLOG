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
        $attempt = 0;
        $maxAttempts = 3;
        $delay = 1; // delay in seconds

        while ($attempt < $maxAttempts) {
            try {
                $response = $this->client->post('chat/completions', [
                    'json' => [
                        'model'  => 'gpt-3.5-turbo', // Atualize conforme necessário
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                        ],
                        'max_tokens' => 150,
                    ],
                ]);

                $body = json_decode($response->getBody(), true);

                return $body['choices'][0]['message']['content'] ?? '';
            } catch (RequestException $e) {
                if ($e->getResponse()->getStatusCode() == 429) {
                    // Espera antes de tentar novamente
                    sleep($delay);
                    $attempt++;
                    $delay *= 2; // Exponential backoff
                } else {
                    // Lança exceção para outros erros
                    throw $e;
                }
            }
        }

        throw new \Exception('Exceeded maximum retry attempts');
    }
}
