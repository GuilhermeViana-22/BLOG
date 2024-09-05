<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagsRequest;
use App\Http\Requests\UpdateTagsRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

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
                'message' => 'Erro ao tentar salvar o post.',
                'error' => 'Erro ao salvar a tag.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Tag $tags)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagsRequest $request, Tag $tags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tags)
    {
        //
    }
}
