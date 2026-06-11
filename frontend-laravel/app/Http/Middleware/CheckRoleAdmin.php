<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');
        
        if (!$user || ($user['role'] ?? '') !== 'administrator') {
            return redirect('/')->with('error', 'Hanya Administrator yang dapat mengakses halaman pengelolaan pengguna.');
        }

        return $next($request);
    }
}
