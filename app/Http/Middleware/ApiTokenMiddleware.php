<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Token; // Certifique-se de ter o modelo Token importado
use Illuminate\Support\Facades\Auth;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Supondo que você está recuperando o token do banco de dados
        $user = Auth::user();
        if ($user) {
            $tokenRecord = Token::where('user_id', $user->id)->first();
            if ($tokenRecord) {
                $token = $tokenRecord->token;
                $request->headers->set('Authorization', 'Bearer ' . $token);
            } else {
                // Se não houver token, você pode optar por retornar um erro ou outro tratamento
                return response()->json(['error' => 'Token não encontrado.'], 401);
            }
        } else {
            // Se não houver um usuário autenticado
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        return $next($request);
    }
}
