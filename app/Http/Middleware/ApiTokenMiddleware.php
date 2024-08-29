<?php

namespace App\Http\Middleware;

use App\Interfaces\AppRequestInterface;
use Closure;
use Illuminate\Http\Request;
use App\Models\Token; // Certifique-se de ter o modelo Token importado
use Illuminate\Support\Facades\Auth;

class ApiTokenMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request instanceof AppRequestInterface) {
            $authorizationHeader = $request->getAuthorizationHeader();
            if ($authorizationHeader) {
                $request->headers->set('Authorization', $authorizationHeader);
            }
        }

        return $next($request);
    }
}
