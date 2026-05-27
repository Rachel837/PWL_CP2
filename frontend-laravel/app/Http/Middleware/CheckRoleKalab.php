<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleKalab
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');
        \Illuminate\Support\Facades\Log::info('CheckRoleKalab Middleware Execution', [
            'has_user' => !empty($user),
            'user_data' => $user,
            'role' => $user['role'] ?? 'none'
        ]);
        
        if (!$user || ($user['role'] ?? '') !== 'kepala laboratorium') {
            return redirect()->route('users.index')->with('error', 'Hanya Kepala Laboratorium yang dapat mengakses halaman pengadaan.');
        }

        return $next($request);
    }
}
