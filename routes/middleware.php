<?php

// This file is used to explicitly register route middleware in Laravel 11.
// Add your middleware here as key-value pairs.

return [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
];