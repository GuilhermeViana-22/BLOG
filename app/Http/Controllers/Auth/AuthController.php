<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HttpHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\APISGP\APISGP;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    protected $apiSgp;

    public function __construct(APISGP $apiSgp)
    {
        $this->apiSgp = $apiSgp;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();

        try {
            $response = $this->apiSgp->post('/api/register', $userData);

            if ($response->successful()) {
                return response()->json(['message' => $response->json()], HttpHelper::HTTP_OK);
            }
            return response()->json(['message' => 'Erro ao tentar registrar usuário.'], HttpHelper::HTTP_FORBIDDEN);
        } catch (RequestException $e) {
            return response()->json([
                'message' => 'Erro ao comunicar com a API externa.',
                'error' => $e->getMessage()
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
