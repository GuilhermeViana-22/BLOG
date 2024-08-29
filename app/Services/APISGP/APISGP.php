<?php

namespace App\Services\APISGP;

use Illuminate\Support\Facades\Http;

class APISGP
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_URL'); // Base URL da API
    }

    /**
     * Envia uma requisição GET para a API externa.
     *
     * @param string $endpoint
     * @param array $params
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $endpoint, array $params = [])
    {
        return Http::get($this->baseUrl . $endpoint, $params);
    }

    /**
     * Envia uma requisição POST para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function post(string $endpoint, array $data = [])
    {
        return Http::post($this->baseUrl . $endpoint, $data);
    }

    /**
     * Envia uma requisição PUT para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function put(string $endpoint, array $data = [])
    {
        return Http::put($this->baseUrl . $endpoint, $data);
    }

    /**
     * Envia uma requisição DELETE para a API externa.
     *
     * @param string $endpoint
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    public function delete(string $endpoint, array $data = [])
    {
        return Http::delete($this->baseUrl . $endpoint, $data);
    }
}
