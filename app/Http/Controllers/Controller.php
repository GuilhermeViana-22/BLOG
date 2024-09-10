<?php

namespace App\Http\Controllers;

use App\Helpers\HttpHelper;
use App\Models\Activity;
use App\Models\FailedJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
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
    public function logFailedJob(array $data, Exception $e)
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
    public function actions(string $action, int $userId, string $categoryName)
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
    public function sucesso(string $res, $message)
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
    public function erro(string $action, \Exception $e)
    {
        $message = $this->getErrorMessage($action);
        Log::error($message . ' Detalhes: ' . $e->getMessage());

        // Apenas a mensagem genérica é retornada na resposta
        return response()->json([
            'message' => $message,
            'error' => $e->getMessage().'Detalhes adicionais não disponíveis.',
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
    public function getErrorMessage(string $action): string
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
    public function getActionDescription(string $action): string
    {
        $descriptions = [
            'delete' => 'Deletou um post',
            'save' => 'Criou ou atualizou um item',
            'update' => 'Atualizou um item',
        ];

        return $descriptions[$action] ?? 'Realizou uma ação';
    }
}
