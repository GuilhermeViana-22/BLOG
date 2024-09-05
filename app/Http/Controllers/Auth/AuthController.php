<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\HttpHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\MeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Models\Token;
use App\Models\User;
use App\Services\APISGP\APISGP;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /***
     * método para realizar o registro de novos usuários dentro da aplicação
     * @param RegisterRequest $request
     * @return JsonResponse
     */
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

    /**
     * Método para realizar o login dentro da aplicação
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $response = $this->apiSgp->post('/login', $credentials);

        if (!$response->successful()) {
            return response()->json(['message' => 'Credenciais inválidas.'], HttpHelper::HTTP_NOT_FOUND);
        }

        $data = $response->json();
        $user_id = (int) $data['user_id'];
        $tokenStr = $data['token'];

        // Armazenar o token e o user_id na sessão
        session([
            'user_id' => $user_id,
            'token' => $tokenStr
        ]);

        try {
            // Verifica se já existe um token para o user_id
            $token = Token::where('user_id', $user_id)->first();

            if ($token) {
                // Se o token já existe, apenas atualiza o valor do token
                $token->token = $tokenStr;
                $token->updated_at = now(); // Atualiza a data de modificação
            } else {
                // Se o token não existe, cria um novo registro
                $token = new Token();
                $token->user_id = $user_id;
                $token->token = $tokenStr;
            }

            $token->save(); // Salva as alterações (inserção ou atualização)
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro entre a comunicação da API e o blog.'], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Login bem-sucedido.',
            'token' => $tokenStr,
            'user_id' => $user_id,
        ], HttpHelper::HTTP_CREATED);
    }



    /***
     * @param MeRequest $request
     * @return JsonResponse
     */
    public function me(MeRequest $request)
    {
        $user_id = $request->get('id', 1);

        $token = Token::where('user_id', $user_id)->first();

        if (!$token) {
            return response()->json(['error' => 'Token não encontrado para o usuário.'], HttpHelper::HTTP_NOT_FOUND);
        }

        $bearerToken = 'Bearer ' . $token->token;

        // Faz a requisição GET para o endpoint /me usando o APISGP
        $response = $this->apiSgp->get('me', ['id' => $user_id], ['Authorization' => $bearerToken, ]);

        if ($response->successful()) {
            return response()->json($response->json(), HttpHelper::HTTP_OK);
        } else {
            return response()->json(['error' => 'Não foi possível encontrar o usuário solicitado, tente novamente.'], HttpHelper::HTTP_NOT_FOUND);
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

    /**
     * Método para realizar o logout da aplicação
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        // Recupera o user_id e o token da sessão
        $user_id = session('user_id');
        $token = session('token');

        if (!$user_id || !$token) {
            return response()->json(['message' => 'Usuário não autenticado.'], HttpHelper::HTTP_UNAUTHORIZED);
        }

        // Localiza o token associado ao usuário no banco de dados
        $tokenRecord = Token::where('user_id', $user_id)
            ->where('token', $token)
            ->first();

        if ($tokenRecord) {
            // Revoga o token (exclui o registro do token)
            $tokenRecord->delete();

            // Limpa os dados da sessão
            session()->forget(['user_id', 'token']);

            return response()->json(['message' => 'Logout realizado com sucesso.'], HttpHelper::HTTP_OK);
        } else {
            return response()->json(['message' => 'Token não encontrado.'], HttpHelper::HTTP_NOT_FOUND);
        }
    }


}
