<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Registrar exceções para análise
            Log::error('Exceção capturada:', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                // Exemplo: Retornar um erro 404 para recursos não encontrados
                return response()->json([
                    'error' => 'Recurso não encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                // Exemplo: Retornar o erro HTTP com o código de status apropriado
                return response()->json([
                    'error' => 'Erro HTTP: ' . $e->getMessage()
                ], $e->getStatusCode());
            }

            // Exemplo: Retornar um erro genérico para exceções não tratadas
            return response()->json([
                'error' => 'Ocorreu um erro interno no servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'errors' => $exception->validator->errors(),
            ], 422);
        }

        return parent::render($request, $exception);
    }
}
