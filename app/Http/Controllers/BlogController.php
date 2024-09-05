<?php

namespace App\Http\Controllers;

use App\Helpers\HttpHelper;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\PostStoreRequest;
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
     * Método para exibir o formulário para editar um post existente.
     * (Aqui, você pode adicionar lógica para exibir uma vista, se necessário.)
     */
    public function edit($id)
    {
        try {
            $post = Post::findOrFail($id);
            return response()->json(['post' => $post]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar o post para edição: ' . $e->getMessage());
            return response()->json(['error' => 'Post não encontrado.'], 404);
        }
    }

    /**
     * Método para atualizar um post existente.
     */
    public function update(PostStoreRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        try {
            // Encontra o post ou lança uma exceção se não encontrado
            $post = Post::findOrFail($id);

            // Atualiza os campos do post com os dados validados
            $post->fill($data);
            $post->save();

            // Carregar a categoria associada ao post, se necessário
            $categoryName = $post->category ? $post->category->name : 'Categoria não encontrada';


            // Cria a atividade
            Activity::create([
                'user_id' => $request->get('user_id'), // Obtém o ID do usuário autenticado
                'action' => 'Atualizou um post',
                'category' => $categoryName, // ou a categoria associada ao post
            ]);


            return response()->json(['success' => 'Post atualizado com sucesso!'], 200);
        } catch (QueryException $e) {
            Log::error('Erro ao atualizar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao tentar atualizar o post.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar atualizar o post. Por favor, tente novamente mais tarde.'], 500);
        }
    }

    /**
     * Método para deletar um post existente.
     */
    public function delete(DeletePostRequest $request)
    {

        DB::beginTransaction();

        try {
            // Encontra o post ou lança uma exceção se não encontrado
            $post = Post::findOrFail($request->get('id'));

            // Deleta o post
            $post->delete();

            // Cria a atividade
            Activity::create([
                'user_id' => $request->get('user_id'), // Obtém o ID do usuário autenticado
                'action' => 'Deletou um post',
                'category' => $post->category ? $post->category->name : 'Categoria não encontrada',
            ]);
            // Commit da transação
            DB::commit();
            return response()->json(['success' => 'Post deletado com sucesso!'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Erro ao deletar o post: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar deletar o post.',
                'error' => $e->getMessage(),
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar o post: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar deletar o post.',
                'error' => $e->getMessage(),
            ], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
