<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;
use App\Helpers\HttpHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
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
    public function store(PostStoreRequest $request): JsonResponse
    {

        $data = $request->validated();
        $post = new Post();

        DB::beginTransaction();
        $post->fill($data);

        try {
            $post->save();
            DB::commit();
            return response()->json(['success' => 'Post salvo com sucesso!'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao tentar salvar o post: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar o post: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro ao tentar salvar o post. Por favor, tente novamente mais tarde.'], 500);
        }
    }

}
