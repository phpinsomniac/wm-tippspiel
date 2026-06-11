<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyEmailDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = config('company_contest.email_domain');

        if (! $domain || ! $request->user()) {
            return $next($request);
        }

        abort_unless(str_ends_with(strtolower($request->user()->email), '@'.strtolower($domain)), 403);

        return $next($request);
    }
}
