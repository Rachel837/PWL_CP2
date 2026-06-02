<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleKaprodi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');
        
        if (!$user || ($user['role'] ?? '') !== 'ketua program studi') {
            return redirect()->route('users.index')->with('error', 'Hanya Ketua Program Studi yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
