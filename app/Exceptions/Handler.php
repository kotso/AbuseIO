<?php

namespace AbuseIO\Exceptions;

use AbuseIO\Traits\Api;
use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
//use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use Api;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,

    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            if ($request->wantsJson()) {
                return $this->errorNotFound($exception->getMessage());
            }
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        }

        if ($request->wantsJson() && !($exception instanceof \Illuminate\Validation\ValidationException)) {
            return $this->errorInternalError($exception->getMessage());
        }

        if ($request->wantsJson() && $exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->response($exception->errors());
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

//    /**
//     * Convert a validation exception into a JSON response.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \Illuminate\Validation\ValidationException  $exception
//     * @return \Illuminate\Http\JsonResponse
//     */
//    protected function invalidJson($request, ValidationException $exception)
//    {
//        return response()->json($exception->errors(), $exception->status);
//    }
}
