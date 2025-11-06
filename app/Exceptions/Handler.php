<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            if ($e instanceof AuthenticationException) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            if ($e instanceof AuthorizationException) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
            $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            return response()->json([
                'error' => class_basename($e),
                'message' => $e->getMessage(),
            ], $code);
        }
        return parent::render($request, $e);
    }
}
