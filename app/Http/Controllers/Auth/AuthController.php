<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HttpHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Models\User;
use App\Services\APISGP\APISGP;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;


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

    /***
     * método para realizar a autorização via codificação recebida.
     */
    public function verifycode(VerifyCodeRequest $request)
    {
        // Recebe o código de validação
        $userData = $request->validated();
        DB::beginTransaction();

        try {


            $response = Http::post('https://apisgp.com/api/verifycode', $userData);

            if (!$response->successful()) {
                DB::rollBack();
                return response()->json(['message' => 'Erro ao verificar o código.'], HttpHelper::HTTP_FORBIDDEN);
            }

            $responseData = $response->json();
            $userDataFromApi = $responseData['user'];
            $token = $responseData['token'];

            // Organiza e cria uma nova instância do modelo User
            $user = $this->organizeUserData($userDataFromApi, $token);
            $user->save();

            DB::commit();
            return response()->json(['message' => $response->json()], HttpHelper::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao comunicar com a API externa.',
                'error' => $e->getMessage()
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Organize and format user data from the API response.
     *
     * @param array $userDataFromApi
     * @param string $token
     * @return User
     */
    protected function organizeUserData(array $userDataFromApi, string $token): User
    {
        // Cria uma nova instância do modelo User
        $user = new User();
        $user->id = $userDataFromApi['id'];
        $user->name = $userDataFromApi['name'];
        $user->email = $userDataFromApi['email'];
        $user->email_verified_at = $userDataFromApi['email_verified_at'];
        $user->remember_token = $token;
        $user->password = 'acd=1234';
//        $user->situacao_id = $userDataFromApi['situacao_id'];
//        $user->created_at = $userDataFromApi['created_at'];
//        $user->active = $userDataFromApi['active'];
        $user->updated_at = $userDataFromApi['updated_at'];


        return $user;
    }

}
