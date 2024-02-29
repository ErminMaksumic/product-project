<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next)
    {
        $logChannel = 'request_logs';

        Log::channel($logChannel)->info('Request:', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'query_params' => $request->query(),
            'body_params' => $request->getContent(),
        ]);

        return $next($request);
    }
}
