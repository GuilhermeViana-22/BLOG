<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HttpHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Models\Token;
use App\Models\User;
use App\Services\APISGP\APISGP;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Mockery\Exception;


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
            $response = $this->apiSgp->post('/register', $userData);

            if ($response->successful()) {
                return response()->json(['message' => $response->json()], HttpHelper::HTTP_OK);
            }
            return response()->json(['message' => 'Erro ao tentar registrar usuário.'], HttpHelper::HTTP_FORBIDDEN);
        } catch (RequestException $e) {
            return response()->json(['message' => 'Erro ao comunicar com a API externa.','error' => $e->getMessage()], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $response = $this->apiSgp->post('/login', $credentials);

        if (!$response->successful()) {
            return response()->json(['message' => 'Credenciais inválidas.'], HttpHelper::HTTP_NOT_FOUND);
        }

        $data = $response->json();

        $user_id = (int) $data['user_id'];
        $token = $data['token'];

        // Armazenar o token usando o método privado
        $this->storeToken($user_id, $token);

        return response()->json(['message' => 'Login bem-sucedido.'],HttpHelper::HTTP_CREATED);
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
            $response = $this->apiSgp->post('/verifycode', $userData);

            if (!$response->successful()) {
                DB::rollBack();
                return response()->json(['message' => 'Erro ao verificar o código, tente novamente.'], HttpHelper::HTTP_FORBIDDEN);
            }

            $data = $response->json();
            $userDataFromApi = $data['user'];
            $token = $data['token'];

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
        $user->id =  (int)  $userDataFromApi['id'];
        $user->name = $userDataFromApi['name'];
        $user->email = $userDataFromApi['email'];
        $user->email_verified_at = $userDataFromApi['email_verified_at'];
        $user->remember_token = $token;
        //esta linha precisa de correção
        $user->password = $userDataFromApi['password'];
        $user->situacao_id = (int) $userDataFromApi['situacao_id'];
        $user->created_at = $userDataFromApi['created_at'];
        $user->active =  (int)  $userDataFromApi['active'];
        $user->updated_at = $userDataFromApi['updated_at'];
        $user->delete_at = $userDataFromApi['delete_at'];


        return $user;
    }

    /**
     * Armazena ou atualiza o token no banco de dados.
     *
     * @param int $user_id
     * @param string $token
     * @return JsonResponse
     */
    private function storeToken(int $user_id, string $token)
    {
        try {
            // Verifica se já existe um registro com o user_id
            $existingToken = Token::where('user_id', $user_id)->first();

            if ($existingToken) {
                // Atualiza o token se o user_id já existir
                $existingToken->token = $token;
                $existingToken->save();
            } else {
                // Cria um novo registro se não existir
                $newToken = new Token();
                $newToken->user_id = $user_id;
                $newToken->token = $token;
                $newToken->save();
            }

            return response()->json(['message' => 'Token salvo com sucesso.']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Não foi possível salvar o token. Verifique os dados e tente novamente. ' . $e->getMessage()
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
