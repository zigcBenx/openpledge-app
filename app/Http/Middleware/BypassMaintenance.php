<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BypassMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        return $next($request);
        // This was for testing
        $allowedUsers = [
            'zigac.benko@gmail.com',
            'hunterteammovies@gmail.com',
            'neja@openpledge.io',
            'ng.benko@gmail.com',
        ];

        if (Auth::check() && in_array(Auth::user()->email, $allowedUsers)) {
            return $next($request);
        }

        if (
            $request->path() == 'login'
            || $request->path() == 'github/webhook'
            || $request->path() == 'auth/github/callback'
            || $request->path() == 'auth/github'
        ) {
            return $next($request);
        }
        return abort(503, 'The application is under maintenance.');
    }
}
