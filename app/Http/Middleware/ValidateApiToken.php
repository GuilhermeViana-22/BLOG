<?php

namespace App\Http\Middleware;

use App\Models\Token;
use App\Services\APISGP\APISGP;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ValidateApiToken
{
    protected $apiSgp;

    public function __construct(APISGP $apiSgp)
    {
        $this->apiSgp = $apiSgp;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        // Obtém o token do cabeçalho Authorization
        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        // Verifica se o token foi fornecido
        if (!$token) {
            return response()->json(['error' => 'Token não fornecido.'], 401);
        }

        $tokenRecord = Token::where('token', $token)->first();

        if (!$tokenRecord) {
            return response()->json(['error' => 'Token não encontrado.'], 401);
        }

        $data = [
            'user_id' => $tokenRecord->user_id,
            'token' => $tokenRecord->token,
        ];

        try {
            // Faz a requisição POST e envia o corpo JSON
            $response = $this->apiSgp->get('/teste', $data);

            // Verifica a resposta
            if (!$response->successful()) {
                return response()->json(['error' => 'api não respondeu corretamente'.$response->json()], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao se comunicar com a API - OU TOKEN NÃO ESTÁ MAIS VALIDO: ' . $e->getMessage()], 500);
        }

        return $next($request);
    }


}
