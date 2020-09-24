<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $debug = env('APP_DEBUG');

        if (!$debug) {
            if ($exception instanceof ValidationException) {
                return new JsonResponse([
                    'error' => 'bad request'
                ], Response::HTTP_BAD_REQUEST);
            } elseif ($exception instanceof BadRequestHttpException) {
                $message = $exception->getMessage();

                if (substr($exception->getMessage(), 0, 4) == 'msg_') {
//                    if ()
                    $message = substr($message, 4);
                    if ($message) {
                        $msg = json_decode($message);
                        if ($msg) {
                            return new JsonResponse([
                                'error' => 'bad request',
                                'message' => $msg
                            ], Response::HTTP_BAD_REQUEST);
                        } else {
                            return new JsonResponse([
                                'error' => 'bad request',
                                'message' => $message
                            ], Response::HTTP_BAD_REQUEST);
                        }
                    }
                    return new JsonResponse([
                        'error' => 'bad request'
                    ], Response::HTTP_BAD_REQUEST);

                }
            } elseif ($exception instanceof ModelNotFoundException) {
                return new JsonResponse([
                    'error' => 'model not found'
                ], Response::HTTP_NOT_FOUND);
            } elseif ($exception instanceof UnauthorizedException) {
                return new JsonResponse([
                    'error' => 'unauthorized exception'
                ], Response::HTTP_UNAUTHORIZED);
            } else {
                return new JsonResponse([
                    'error' => $exception->getMessage()
                ]);
            }
        }
        return parent::render($request, $exception);
    }
}
