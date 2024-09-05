<?php

namespace App\Http\Middleware;

use App\Services\APISGP\APISGP;
use Closure;
use Illuminate\Http\Request;

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
        $userId = $request->input('user_id');
        $token = $request->header('Authorization');

        // Remove o prefixo "Bearer " se estiver presente no token
        $token = str_replace('Bearer ', '', $token);

        // Chamada para a primeira API para validar o token
        $data = ['user_id' => $userId, 'token' => $token];
        $response = $this->apiSgp->post('/validate-token', $data);
        $responseData = $response->json();

        if (!$responseData['authorized']) {
            return response()->json(['error' => 'Token inválido ou não autorizado.'], 401);
        }

        return $next($request);
    }
}
