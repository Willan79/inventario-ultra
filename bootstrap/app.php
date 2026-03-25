<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\RepositoryServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(fn() => route('web.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $user = auth()->user();
            $roleName = $user && $user->getRoleNames()->isNotEmpty() 
                ? ucfirst($user->getRoleNames()->first()) 
                : 'Usuario';

            if ($request->header('Referer')) {
                return redirect()
                    ->back()
                    ->with('error', "Acceso denegado. Tu rol ({$roleName}) no tiene permisos para esta sección.");
            }

            return response()->view('errors.403', [
                'exception' => $e,
                'roleName' => $roleName
            ], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            if ($request->header('Referer')) {
                return redirect()
                    ->back()
                    ->with('error', 'No tienes permisos para realizar esta acción.');
            }

            return response()->view('errors.403', [
                'exception' => $e
            ], 403);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recurso no encontrado.',
                    'error' => '404'
                ], 404);
            }
        });
    })->create();
