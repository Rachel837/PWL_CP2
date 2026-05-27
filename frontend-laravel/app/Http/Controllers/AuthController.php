<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    public function showLoginForm()
    {
        // Jika sudah login, redirect ke halaman users
        if (Session::has('user')) {
            return redirect()->route('users.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $response = Http::post("{$this->apiUrl}/login", [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $user = $response->json('data');
                Session::put('user', $user);
                return redirect()->route('users.index')->with('success', 'Berhasil login!');
            }

            return back()->withErrors(['email' => $response->json('message') ?? 'Login gagal, periksa email dan password.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Tidak dapat terhubung ke server backend.'])->withInput();
        }
    }

    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login');
    }
}
