<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        if ($exception instanceof QuestAlreadyCompletedException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        if ($exception instanceof QuestAlreadyCompletedException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return parent::render($request, $exception);
    }
}
