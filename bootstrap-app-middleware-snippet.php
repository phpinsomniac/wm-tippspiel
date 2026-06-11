<?php
// In Laravel 11/12/13 in bootstrap/app.php in den withMiddleware-Block einfuegen:
//
// ->withMiddleware(function (Middleware $middleware): void {
//     $middleware->alias([
//         'admin' => \App\Http\Middleware\EnsureAdmin::class,
//         'company.email' => \App\Http\Middleware\EnsureCompanyEmailDomain::class,
//     ]);
// })
