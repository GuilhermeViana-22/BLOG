<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Activity;
use App\Helpers\HttpHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Http\Requests\Posts\PostStoreRequest;
use App\Http\Requests\Posts\DeletePostRequest;
use App\Http\Requests\Posts\PostUpdateRequest;

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
}
