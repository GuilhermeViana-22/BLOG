<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
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
            return $this->erro('create', $e);
        }
    }

}
