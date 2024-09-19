<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;



class UserController extends Controller
{
    /**
     * método de busca de usuarios
     */
    public function index(){

    }



    /**
     * Atualiza os dados do usuário.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function atualizar(UpdateUserRequest $request)
    {
        $user = User::findOrFail($request->route('user_id'));

        // Atualizar a senha se fornecida
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Atualizar a foto se fornecida
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists('public/' . $user->photo)) {
                Storage::delete('public/' . $user->photo);
            }

            $photo = $request->file('photo');
            $photoPath = $photo->store('photos', 'public');
            $user->photo = $photoPath;
        }

        // Atualizar os dados usando mass assignment
        $user->fill($request->except(['password', 'photo']));

        // Salvar as alterações
        $user->save();

        return response()->json(['message' => 'Usuário atualizado com sucesso.']);
    }


}
