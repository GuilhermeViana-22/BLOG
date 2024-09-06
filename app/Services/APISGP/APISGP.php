<?php

namespace App\Services\APISGP;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class APISGP
{
    protected $baseUrl;

    public function __construct()
    {
        // Define a URL base garantindo que termine com uma barra
        $this->baseUrl = rtrim(env('API_URL'), '/') . '/';
    }


    /**
     * Envia uma requisição GET para a API externa.
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $endpoint, array $params = [], array $headers = []): Response
    {
        return $this->sendRequest('get', $endpoint, $params, $headers);
    }


    /**
     * Envia uma requisição POST para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->sendRequest('post', $endpoint, $data);
    }

    /**
     * Envia uma requisição PUT para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function put(string $endpoint, array $data = []): Response
    {
        return $this->sendRequest('put', $endpoint, $data);
    }

    /**
     * Envia uma requisição DELETE para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function delete(string $endpoint, array $data = []): Response
    {
        return $this->sendRequest('delete', $endpoint, $data);
    }

    /**
     * Constrói e envia a requisição para a API externa.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     */
    protected function sendRequest(string $method, string $endpoint, array $data = [], array $headers = []): Response
    {
        // Garante que o endpoint comece com uma barra, mas sem duplicar barras na URL final
        $url = $this->baseUrl . ltrim($endpoint, '/');
  
        Log::info("Sending {$method} request to {$url}", ['data' => $data, 'headers' => $headers]);
    
          // Verifica o APP_URL e decide se deve desativar a verificação SSL
          $verifySsl = $this->shouldVerifySsl();

        try {
            // Adiciona a opção 'verify' => false para desativar a verificação SSL
            $response = Http::withOptions([
                'verify' => $verifySsl, // Desativa a verificação SSL
            ])->withHeaders($headers)->{$method}($url, $data);
            Log::info("Response received", ['body' => $response->body()]);
            return $response;
        } catch (RequestException $e) {
            // Registra o erro e lança a exceção
            Log::error("Request failed: {$e->getMessage()}", ['url' => $url, 'data' => $data]);
            throw $e;
        }
    }
    
     /**
     * Decide se a verificação SSL deve ser ativada ou desativada.
     *
     * @return bool
     */
    protected function shouldVerifySsl(): bool
    {
        $appUrl = env('APP_URL', ''); // Obtém o valor de APP_URL do arquivo .env
        return $appUrl !== 'http://127.0.0.1:8000'; // Verifica se o APP_URL é diferente
    }

}

