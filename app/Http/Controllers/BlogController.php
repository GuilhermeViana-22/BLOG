<?php

namespace App\Http\Controllers;

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
        $posts = Post::all();
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

            // Cria a atividade
            Activity::create([
                'user_id' => $request->user()->id, // Obtém o ID do usuário autenticado
                'action' => 'Criou um novo post',
                'category' => $post->category->name, // ou a categoria associada ao post
            ]);

            // Commit da transação
            DB::commit();

            return response()->json(['success' => 'Post salvo com sucesso!'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao tentar salvar o post.'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar salvar o post. Por favor, tente novamente mais tarde.'], 500);
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
            $post = Post::findOrFail($id);
            $post->fill($data);
            $post->save();
            return response()->json(['success' => 'Post atualizado com sucesso!'], 200);
        } catch (QueryException $e) {
            Log::error('Erro ao atualizar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao tentar atualizar o post.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar atualizar o post.'], 500);
        }
    }

    /**
     * Método para deletar um post existente.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();
            return response()->json(['success' => 'Post deletado com sucesso!'], 200);
        } catch (QueryException $e) {
            Log::error('Erro ao deletar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao tentar deletar o post.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar deletar o post.'], 500);
        }
    }
}
