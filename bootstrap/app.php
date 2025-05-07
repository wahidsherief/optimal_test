<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        channels: __DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('all.auth', [
            \App\Http\Middleware\AuthenticateMultipleGuards::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Handle resource/route not found exception
        // $exceptions->render(function (\Exception $e, $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status' => 'fail',
        //             'error' => 'Something went wrong.',
        //         ], 500);
        //     }
        // });


        // Handle resource/route not found exception
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle route not found exception
        $exceptions->render(function (RouteNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthenticated route',
                ], 404);
            }
        });

        // Handle route not found exception
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Method is not allowed',
                ], 404);
            }
        });

        // Query Exceptions or Database Error
        $exceptions->render(function (QueryException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Database error.',
                'errors' => $e->getMessage(),
            ], 500);
        });

        // Handle AuthenticationException
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthenticated',
                ], 401);
            }
            // If there's no web route, return a JSON response for all requests
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthenticated',
            ], 401);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation failed.',
                    'errors' => $e->getMessage()
                ], 401);
            }
            // If there's no web route, return a JSON response for all requests
            return response()->json([
                'status' => 'error',
                'messsage' => 'Unauthenticated',
            ], 401);
        });
    })->create();
