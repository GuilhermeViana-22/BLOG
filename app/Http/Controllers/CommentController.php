<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    /**
     *Store a newly created resource in storage.
     */
    public function comentar(StoreCommentRequest $request)
    {
        // Validação dos dados
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Criação do comentário
            $comment = new Comment();
            $comment->fill($data);

            // Certifica que o comentário está relacionado ao post
            $post = Post::findOrFail($data['post_id']); // Lança um erro se o post não for encontrado

            // Associa o comentário ao post
            $comment->post()->associate($post);

            // Salva o comentário na tabela de comentários
            $comment->save();

            // Confirma a transação
            DB::commit();

            // Retorna uma resposta de sucesso
            return $this->sucesso('create', 'Comentário adicionado com sucesso!');
        } catch (\Exception $e) {
            // Reverte a transação em caso de erro
            DB::rollBack();

            // Loga o erro (opcional)
            Log::error('Erro ao salvar comentário: ' . $e->getMessage());

            // Retorna uma resposta de erro
            return $this->erro('erro', $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deletarComentario(Request $request)
    {
        DB::beginTransaction();

        try {
            // Encontra o comentário pelo ID ou lança um erro se não for encontrado
            $comment = Comment::findOrFail($request->get('id'));

            // Deleta o comentário
            $comment->delete();

            // Confirma a transação
            DB::commit();

            // Retorna uma resposta de sucesso
            return $this->sucesso('delete', 'Comentário deletado com sucesso!');
        } catch (\Exception $e) {
            // Reverte a transação em caso de erro
            DB::rollBack();

            // Loga o erro (opcional)
            Log::error('Erro ao deletar comentário: ' . $e->getMessage());

            // Retorna uma resposta de erro
            return $this->erro('erro', $e);
        }
    }

    /**
     * Incrementa o contador de likes de um comentário específico.
     */
    public function incrementarLikes(Request $request)
    {
        DB::beginTransaction();

        try {
            // Encontra o comentário pelo ID ou lança um erro se não for encontrado
            $comment = Comment::findOrFail($request->get('id'));

            // Incrementa o contador de likes
            $comment->increment('likes_count');

            // Confirma a transação
            DB::commit();

            // Retorna uma resposta de sucesso
            return $this->sucesso('update', 'Contador de likes incrementado com sucesso!');
        } catch (\Exception $e) {
            // Reverte a transação em caso de erro
            DB::rollBack();

            // Loga o erro (opcional)
            Log::error('Erro ao incrementar contador de likes: ' . $e->getMessage());

            // Retorna uma resposta de erro
            return $this->erro('erro', $e);
        }
    }
}
