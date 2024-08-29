<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Atualiza uma categoria existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'relevant' => 'nullable|boolean',
            'published' => 'nullable|date',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json([
            'message' => 'Categoria atualizada com sucesso!',
            'category' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Armazena uma nova categoria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @return \Illuminate\Http\JsonResponse
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
