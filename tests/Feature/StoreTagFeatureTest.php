<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Illuminate\Http\Response;

class StoreTagFeatureTest extends TestCase
{

    public function testStoreTagSuccessfully()
    {
        // Dados de teste
        $data = [
            'name' => 'PHP Unit',
            'slug' => 'php-unit',
        ];

        // Simula a requisição
        $response = $this->postJson('/api/tags', $data);

        // Verifica a resposta
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'message' => 'Tag criada com sucesso!',
            'data' => $data
        ]);

        // Verifica se a tag foi salva no banco de dados
        $this->assertDatabaseHas('tags', $data);
    }

    public function testStoreTagFailsWithException()
    {
        // Dados de teste
        $data = [
            'name' => 'PHP Unit Error',
            'slug' => 'php-unit-error',
        ];

        $hash = hash('sha512', str_repeat('slug=null', 50));

        $data2 = [
            'name' => 341234, // Dados inválidos
            'slug' => $hash,
        ];

        // Configura o Log para capturar erros
        Log::shouldReceive('error')
            ->once()
            ->with(Mockery::on(function($message) {
                return str_contains($message, 'Erro ao salvar a tag');
            }))
            ->andReturn(true);

        // Simula a ocorrência de uma exceção real
        $this->postJson('/api/tags', $data2)
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => 'Erro ao tentar salvar o post.',
                'error' => 'Erro ao salvar a tag.',
            ]);

    }
}
