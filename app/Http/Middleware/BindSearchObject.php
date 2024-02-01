<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BindSearchObject
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $searchObjectType)
    {
        $searchObjectClass = "App\\Http\\Requests\\SearchObjects\\{$searchObjectType}";
        if (class_exists($searchObjectClass)) {
            $searchObject = new $searchObjectClass($request->query());
            $request->attributes->set('searchObject', $searchObject);
        }

        return $next($searchObject);
    }
}
