<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleStafLab
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
        
        if (!$user || ($user['role'] ?? '') !== 'staf laboratorium') {
            return redirect('/')->with('error', 'Hanya Staf Laboratorium yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
