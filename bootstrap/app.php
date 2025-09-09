<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // --- INÍCIO DA CORREÇÃO ---
        // Adiciona os aliases para que o Laravel saiba o que 'permission' significa
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        // --- FIM DA CORREÇÃO ---
    })
    // ... (parte do .withMiddleware(...))
    ->withExceptions(function (Exceptions $exceptions) {
        // --- INÍCIO DA CORREÇÃO ---
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('inertia')) {
                $statusCode = 500;
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                }

                return \Inertia\Inertia::render('Error', [
                    'status' => $statusCode,
                    'message' => $e->getMessage(),
                ])->toResponse($request)->setStatusCode($statusCode);
            }
        });
        // --- FIM DA CORREÇÃO ---
    })->create();
