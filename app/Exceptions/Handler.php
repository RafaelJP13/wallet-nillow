<?php

namespace App\Exceptions;

use App\Exceptions\Domain\InsufficientFundsException;
use App\Exceptions\Domain\InvalidAmountException;
use App\Exceptions\Domain\UnauthorizedTransactionException;
use App\Exceptions\Domain\WalletNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
  
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {
        if (! $request->expectsJson()) {
            return parent::render($request, $e);
        }

        return match (true) {

            $e instanceof InvalidAmountException => response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'INVALID_AMOUNT',
            ], 422),

            $e instanceof InsufficientFundsException => response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'INSUFFICIENT_FUNDS',
            ], 422),

            $e instanceof WalletNotFoundException => response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'WALLET_NOT_FOUND',
            ], 404),

            $e instanceof UnauthorizedTransactionException => response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'UNAUTHORIZED_TRANSACTION',
            ], 403),

            $e instanceof ModelNotFoundException => response()->json([
                'success' => false,
                'message' => 'Recurso não encontrado.',
                'code' => 'RESOURCE_NOT_FOUND',
            ], 404),

            $e instanceof AuthenticationException => response()->json([
                'success' => false,
                'message' => 'Não autenticado.',
                'code' => 'UNAUTHENTICATED',
            ], 401),

            $e instanceof HttpExceptionInterface => response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Erro HTTP.',
                'code' => 'HTTP_ERROR',
            ], $e->getStatusCode()),

            default => response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Erro interno do servidor.',
                'code' => 'INTERNAL_SERVER_ERROR',
            ], 500),
        };
    }
}