<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPublicApiKey
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $publicApiKey = $request->header('X-Public-Api-Key');
        if (trim($publicApiKey) == config('auth.public_api_key')) {
            return $next($request);
        }

        return $this->error('Forbidden', Response::HTTP_FORBIDDEN);
    }
}
