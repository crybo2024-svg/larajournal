<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrReader
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(auth()->user()->role, ['admin', 'reader'])) {
            abort(403); // AdminでもReaderでもない場合はアクセス拒否
        }

        return $next($request);
    }
}