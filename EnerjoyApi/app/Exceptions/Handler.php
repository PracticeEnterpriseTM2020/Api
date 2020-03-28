<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     * 
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => trans('errors.forbidden')], 403);
        }

        if ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => trans('errors.blacklist-token')], 401);
        }

        if ($exception instanceof JWTException) {
            return response()->json(['error' => trans('errors.ivalid-token')], 401);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => trans('errors.not-found')
            ], 404);
        }

        if ($exception instanceof QueryException) {
            return response()->json([
                'error' => trans('errors.internal')
            ], 500);
        }

        if ((Request::isMethod('post') && $exception instanceof MethodNotAllowedHttpException) || (Request::isMethod('post') && $exception instanceof NotFoundHttpException)) {
            return response()->json(['message' => 'Page Not Found'], 500);
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => trans('errors.unauthenticated')], 401);
    }
}
