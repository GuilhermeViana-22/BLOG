<?php

namespace App\Http\Controllers;

use \Log;
use \Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;



class UserController extends Controller
{
    /**
     * método de busca de usuarios
     */
    public function index(){
       return response()->json('due good');

    }

   /**
     * Atualiza a foto do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request)
    {
        $user = User::findOrFail($request->get('user_id'));

        // Se não tiver foto, apenas atualiza os outros dados
        if (!$request->has('photo')) {
            $user->fill($request->validated());
            $user->save();

            return response()->json(['message' => 'Dados atualizados com sucesso.']);
        }

        // Atualiza os outros dados também, se houver
        $user->fill($request->validated());

        // Se houver uma nova foto, salva e atualiza o campo
        if ($request->has('photo')) {
            try {
                $user->photo = StorageHelper::salvar($request->get('photo'), User::STORAGE_PATH . $user->id);
                $user->save(); // Salva o usuário após atualizar a foto
                return response()->json(['message' => 'Foto e dados atualizados com sucesso.']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erro ao atualizar a foto: ' . $e->getMessage()], 500);
            }
        }

    }





}
