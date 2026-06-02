<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->apiUrl}/users");
        $users = $response->json('data') ?? [];
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $response = Http::get("{$this->apiUrl}/roles");
        $roles = $response->json('data') ?? [];
        return view('users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'roles_id' => 'required'
        ]);

        $response = Http::post("{$this->apiUrl}/users", $request->all());

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Terjadi kesalahan saat menyimpan data')->withInput();
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $responseUser = Http::get("{$this->apiUrl}/users/{$id}");
        $user = $responseUser->json('data');

        $responseRoles = Http::get("{$this->apiUrl}/roles");
        $roles = $responseRoles->json('data') ?? [];

        return view('users.form', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'nullable',
            'roles_id' => 'required'
        ]);

        $response = Http::put("{$this->apiUrl}/users/{$id}", $request->all());

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Terjadi kesalahan saat memperbarui data')->withInput();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        Http::delete("{$this->apiUrl}/users/{$id}");
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
