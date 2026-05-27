<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('user')) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.']);
        }
        return $next($request);
    }
}
