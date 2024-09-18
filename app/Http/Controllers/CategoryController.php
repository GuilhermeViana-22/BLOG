<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\HttpHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Categories\UpdateCategoryRequest;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, HttpHelper::HTTP_OK);
    }


    /**
     * Atualiza uma categoria existente.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request)
    {
        try {
            $category = Category::findOrFail($request->get('id'));
            $category->update($request->all());
            $this->actions('update', $request->get('user_id'), $category->name);
            return $this->sucesso('update', 'Categoria atualizada com sucesso!');
        } catch (\Exception $e) {
            return $this->erro('update', $e);
        }
    }


    /**
     * Armazena uma nova categoria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'relevant' => 'nullable|boolean',
            'published' => 'nullable|date',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Categoria criada com sucesso!',
            'category' => $category
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove uma categoria.
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Categoria excluída com sucesso!'
        ], Response::HTTP_OK);
    }

    /**
     * Atualiza o contador de relevância.
     * @return JsonResponse
     */
    public function incrementRelevant()
    {
        $category = Category::orderBy('relevant', 'desc')->first(); // Obtém a categoria com o maior valor de relevância
        if ($category) {
            $category->increment('relevant');
            return response()->json([
                'message' => 'Relevância atualizada com sucesso!',
                'relevant' => $category->relevant
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Nenhuma categoria encontrada para atualizar a relevância.'
        ], Response::HTTP_NOT_FOUND);
    }
}
