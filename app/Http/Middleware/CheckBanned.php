<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->status === 'banned') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been banned.');
        }

        return $next($request);
    }
}