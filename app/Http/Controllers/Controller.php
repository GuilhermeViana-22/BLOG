<?php

namespace App\Http\Controllers;

use App\Models\FailedJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mockery\Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /***
     * método privado para registrar erros que aconteceram durante a vida util da aplicaçã
     * @param array $data
     * @param Exception $e
     * @return void
     */
    private function logFailedJob(array $data, Exception $e)
    {
        // Salva os detalhes do erro na tabela failed_jobs
        FailedJob::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'connection' => config('queue.default'),
            'queue' => 'default',  // Ou especifique o nome da fila
            'payload' => json_encode($data),  // Salva o payload dos dados
            'exception' => $e->getMessage(),  // Salva a mensagem de exceção
            'failed_at' => now(),  // Data e hora do erro
        ]);
    }
}
