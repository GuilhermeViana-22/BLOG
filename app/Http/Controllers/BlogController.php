<?php

namespace App\Http\Controllers;

use App\Helpers\HttpHelper;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class BlogController extends Controller
{
    /***
     * M�todo que tr�s todos os posts do blog
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json(array('posts' => $posts));
    }

    /***
     * M�todo para realizar a cria��o de novos posts para o blod
     */
    public function store(StorePostRequest $request)
    {
        // Pega os dados validados do request
        $data = $request->validated();
        $post = new Post();

        // Inicia a transação do banco de dados
        DB::beginTransaction();
        $post->fill($data);

        try {
            $post->save();
            return response()->json(['success' => 'O post foi salvo com sucesso'], HttpHelper::HTTP_OK);
        } catch (Exception $e) {
            $this->logFailedJob($request->all(), $e);
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar salvar o post. Por favor, tente novamente mais tarde.'], HttpHelper::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
