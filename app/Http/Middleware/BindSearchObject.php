<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BindSearchObject
{
    public function handle(Request $request, Closure $next, $searchObjectType)
    {
        $searchObjectClass = "App\\Http\\Requests\\SearchObjects\\{$searchObjectType}";

        if (class_exists($searchObjectClass)) {
           $searchObject = new $searchObjectClass($request->query());
        }
        else
        {
            return $next($request);
        }

        return $next($searchObject);
    }
}
