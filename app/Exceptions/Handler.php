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

    // public function render($request, Throwable $e)
    // {
    //     $response = parent::render($request, $e);
    //     $status = $response->status();

    //     return match ($status) {
    //         404 => inertia('Error/404', ['user' => auth()->user()])->toResponse($request)->setStatusCode($status),
    //         419 => inertia('Error/419')->toResponse($request)->setStatusCode($status),
    //         default => $response
    //     };
    // }
}
