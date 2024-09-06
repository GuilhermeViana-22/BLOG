<?php

namespace App\Http\Controllers;

use App\Helpers\HttpHelper;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Activity;
use App\Models\Post;

class BlogController extends Controller
{
    /**
     * Método que traz todos os posts do blog.
     */
    public function index()
    {
        // Capturar os parâmetros da requisição
        $id = request()->query('id');
        $title = request()->query('title');
        $subtitle = request()->query('subtitle');
        $categoryId = request()->query('category_id');
        $createdAt = request()->query('created_at');

        // Iniciar a consulta
        $query = Post::query();

        if ($id) {
            $query->where('id', $id);
        }

        // Aplicar filtros condicionais
        if ($title) {
            $query->where('title', 'LIKE', '%' . $title . '%');
        }

        if ($subtitle) {
            $query->where('subtitle', 'LIKE', '%' . $subtitle . '%');
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($createdAt) {
            // Supondo que created_at seja uma data no formato 'Y-m-d'
            $query->whereDate('created_at', $createdAt);
        }

        // Obter os posts filtrados
        $posts = $query->get();

        // Retornar a resposta JSON
        return response()->json(['posts' => $posts]);
    }


    /**
     * Método para realizar a criação de novos posts para o blog.
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Cria o novo post
            $post = Post::create($data);

            // Carrega a categoria associada ao post, se necessário
            $categoryName = $post->category ? $post->category->name : 'Categoria não encontrada';

            // Cria a atividade
            Activity::create([
                'user_id' => $request->get('user_id'), // Obtém o ID do usuário autenticado
                'action' => 'Criou um novo post',
                'category' => $categoryName, // ou a categoria associada ao post
            ]);

            // Commit da transação
            DB::commit();

            return response()->json(['success' => 'Post salvo com sucesso!'], HttpHelper::HTTP_CREATED); // Código 201 para criação bem-sucedida
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar salvar o post.',
                'error' => $e->getMessage(),
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar salvar o post.',
                'error' => $e->getMessage(),
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Método para exibir um post específico.
     */
    public function show($id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            return response()->json(['post' => $post]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Post não encontrado.'], 404);
        }
    }

    /**
     * Atualiza um post existente.
     *
     * @param PostUpdateRequest $request
     * @return JsonResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(PostUpdateRequest $request): JsonResponse
    {
        // Obtém o ID do post a partir da requisição
        $id = $request->get('id');
        $data = $request->validated();

        // Verifica se o post existe
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'error' => 'Post não encontrado.'
            ], HttpHelper::HTTP_NOT_FOUND);
        }

        // Atualiza o post com os dados validados
        $post->fill($data);

        try {
            $post->save();

            // Ação personalizada, se necessário
            $this->actions('update', $request->get('user_id'), $post->category ? $post->category->name : 'Categoria não encontrada');

            // Retorna uma resposta de sucesso usando o método sucesso
            return $this->sucesso('update', 'Post atualizado com sucesso!');
        } catch (QueryException $e) {
            // Retorna uma resposta de erro detalhada para problemas de consulta
            return $this->erro('update', $e);
        } catch (\Exception $e) {
            // Retorna uma resposta de erro genérica para outros problemas
            return $this->erro('update', $e);
        }
    }




    /**
     * Método para deletar um post existente.
     */
    public function delete(DeletePostRequest $request)
    {
        DB::beginTransaction();

        $post = Post::findOrFail($request->get('id'));

        try {
            $post->delete();
            $this->actions('delete', $request->get('user_id'), $post->category ? $post->category->name : 'Categoria não encontrada');
            DB::commit();
            return $this->sucesso('success', 'Post deletado com sucesso!');
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->erro('delete', $e);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->erro('delete', $e);
        }
    }

    /**
     * Realiza a validação e registro das ações dos usuários na linha de tempo.
     *
     * Este método cria uma nova entrada na tabela de atividades (`Activity`) com base na ação realizada pelo usuário.
     * O método captura e trata exceções durante o processo de criação da atividade, garantindo que erros sejam logados e
     * tratados adequadamente.
     *
     * @param string $action Descrição ou identificador da ação realizada pelo usuário. Deve ser uma string que descreva
     *                        a ação a ser registrada.
     * @param int $userId Identificador único do usuário que realizou a ação. Deve ser um valor numérico que corresponde
     *                    ao ID do usuário no sistema.
     * @param string $categoryName Nome da categoria associada à ação. Deve ser uma string que classifica a ação em uma
     *                              categoria específica para fins de organização e filtragem.
     *
     * @throws \RuntimeException Se ocorrer um erro durante a criação da atividade, uma exceção `RuntimeException` será
     *                            lançada com uma mensagem descritiva do erro.
     *
     * @return void
     */
    private function actions(string $action, int $userId, string $categoryName)
    {
        try {
            Activity::create([
                'user_id' => $userId, // Obtém o ID do usuário autenticado
                'action' => $this->getActionDescription($action),
                'category' => $categoryName,
            ]);
        } catch (\Exception $e) {
            // Log e trata erro na criação da atividade
            Log::error('Erro ao tentar criar a atividade: ' . $e->getMessage());
            throw new \RuntimeException('Erro ao tentar registrar a atividade.', 0, $e);
        }
    }


    /**
     * Retorna uma resposta JSON de sucesso.
     *
     * Este método gera uma resposta JSON indicando que a operação foi bem-sucedida. A resposta inclui um código HTTP 200
     * (OK) e uma estrutura JSON com o resultado da operação.
     *
     * @param string $res Nome da chave no objeto JSON de resposta. A chave será associada ao valor fornecido pelo parâmetro
     *                    `$message`.
     * @param mixed $message Mensagem ou dado associado à chave `$res` na resposta JSON. Pode ser uma string, um número, ou
     *                       qualquer outro tipo de dado.
     *
     * @return \Illuminate\Http\JsonResponse Resposta JSON com código HTTP 200.
     */
    private function sucesso(string $res, $message)
    {
        return response()->json([$res => $message], HttpHelper::HTTP_OK);
    }

    /**
     * Retorna uma resposta JSON de erro e registra a exceção.
     *
     * Este método gera uma resposta JSON para indicar que ocorreu um erro. A resposta inclui um código HTTP 500 (Internal
     * Server Error) e uma mensagem genérica de erro. O método também registra detalhes da exceção para fins de depuração.
     *
     * @param string $action Descrição ou identificador da ação que causou o erro. Utilizado para gerar uma mensagem de erro
     *                       mais específica.
     * @param \Exception $e A exceção lançada durante o processo. Contém detalhes sobre o erro ocorrido.
     *
     * @return \Illuminate\Http\JsonResponse Resposta JSON com código HTTP 500 e uma mensagem de erro genérica.
     */
    private function erro(string $action, \Exception $e)
    {
        $message = $this->getErrorMessage($action);
        Log::error($message . ' Detalhes: ' . $e->getMessage());

        // Apenas a mensagem genérica é retornada na resposta
        return response()->json([
            'message' => $message,
            'error' => 'Detalhes adicionais não disponíveis.',
        ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Obtém a mensagem de erro associada a uma ação específica.
     *
     * Este método retorna uma mensagem de erro personalizada com base na ação fornecida. Se a ação não estiver definida na
     * lista de mensagens, é retornada uma mensagem de erro genérica.
     *
     * @param string $action Ação que causou o erro. Deve corresponder a uma das chaves definidas no array de mensagens.
     *
     * @return string Mensagem de erro correspondente à ação fornecida. Se a ação não estiver definida, retorna uma mensagem
     *                genérica de erro.
     */
    private function getErrorMessage(string $action): string
    {
        $messages = [
            'delete' => 'Erro ao tentar deletar o item.',
            'activity_create' => 'Erro ao tentar criar a atividade.',
            'save' => 'Erro ao tentar salvar o item.',
            'update' => 'Erro ao tentar atualizar o item.',
            // Adicione mais ações conforme necessário
        ];

        return $messages[$action] ?? 'Erro ao tentar realizar a operação.';
    }

    /**
     * Obtém a descrição da ação associada a uma chave específica.
     *
     * Este método retorna uma descrição textual da ação com base na chave fornecida. Se a chave não estiver definida na lista
     * de descrições, é retornada uma descrição genérica.
     *
     * @param string $action Chave que representa a ação. Deve corresponder a uma das chaves definidas no array de descrições.
     *
     * @return string Descrição correspondente à ação fornecida. Se a ação não estiver definida, retorna uma descrição
     *                genérica.
     */
    private function getActionDescription(string $action): string
    {
        $descriptions = [
            'delete' => 'Deletou um post',
            'save' => 'Criou ou atualizou um item',
            'update' => 'Atualizou um item',
            // Adicione mais ações conforme necessário
        ];

        return $descriptions[$action] ?? 'Realizou uma ação';
    }
}
