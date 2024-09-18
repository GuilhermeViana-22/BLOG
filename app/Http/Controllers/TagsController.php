<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreTagsRequest;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagsRequest $request)
    {
        // Valida e sanitiza os dados da requisição
        $validatedData = $request->validated();

        // Cria uma nova instância do modelo Tag e preenche os dados
        $tag = new Tag();
        $tag->fill($validatedData);

        // Salva o modelo no banco de dados
        try {
            // Força uma exceção para dados inválidos
            if (is_numeric($validatedData['name']) || is_null($validatedData['slug'])) {
                throw new \Exception('Dados inválidos para salvar a tag.');
            }

            $tag->save();
            return response()->json([
                'message' => 'Tag criada com sucesso!',
                'data' => $tag
            ], Response::HTTP_CREATED); // Status 201 Created
        } catch (\Exception $e) {
            Log::error('Erro ao salvar a tag: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar salvar a tag.',
                'error' => 'Erro ao salvar a tag.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $search = $request->query('search', '');

        // Encontra tags pelo nome similar
        $tags = Tag::where('name', 'like', "%{$search}%")->get();

        return response()->json([
            'data' => $tags
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        // Aqui você pode retornar uma visualização ou JSON com os detalhes da tag para edição
        return response()->json([
            'data' => $tag
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        try {
            // Remove a tag do banco de dados
            $tag->delete();
            return response()->json([
                'message' => 'Tag deletada com sucesso!'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar a tag: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao tentar deletar a tag.',
                'error' => 'Erro ao deletar a tag.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
