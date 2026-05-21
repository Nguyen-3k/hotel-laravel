<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cấp phép cho SePay gửi dữ liệu vào webhook mà không bị chặn bởi CSRF Token
        $middleware->validateCsrfTokens(except: [
            'sepay/webhook', 
        ]);

        // Khai báo bí danh 'admin' cho middleware phân quyền
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();