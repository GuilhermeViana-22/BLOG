<?php

namespace App\Http\Controllers;

use \Log;
use \Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\StorageHelper;
use App\Http\Resources\UserResource;
use App\Http\Requests\ProfileRequest;
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
     * profile
     *
     * @param  mixed $request
     * @return void
     */
    public function profile(ProfileRequest $request)
    {
        // Busca o usuário pelo ID fornecido na requisição
        $user = User::findOrFail($request->get('user_id'));

        // Transformar a foto em Base64
        if ($user->photo) {
            $path = Storage::path('public/' . User::STORAGE_PATH . $user->id . '/' . $user->photo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $user->photo = $base64;
        }

        // Retorna os dados do usuário em formato de coleção
        return new UserResource($user);
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
