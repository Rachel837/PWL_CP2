<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RuanganController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->apiUrl}/ruangan");
        $ruangan = $response->json('data') ?? [];
        return view('ruangan.index', compact('ruangan'));
    }

    public function create()
    {
        return view('ruangan.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required',
            'nama_ruangan' => 'required'
        ]);

        Http::post("{$this->apiUrl}/ruangan", $request->all());

        return redirect()->route('ruangan.index')->with('success', 'Room created successfully');
    }

    public function edit($id)
    {
        $response = Http::get("{$this->apiUrl}/ruangan/{$id}");
        $ruangan = $response->json('data');

        return view('ruangan.form', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_ruangan' => 'required',
            'nama_ruangan' => 'required'
        ]);

        Http::put("{$this->apiUrl}/ruangan/{$id}", $request->all());

        return redirect()->route('ruangan.index')->with('success', 'Room updated successfully');
    }

    public function destroy($id)
    {
        Http::delete("{$this->apiUrl}/ruangan/{$id}");
        return redirect()->route('ruangan.index')->with('success', 'Room deleted successfully');
    }
}
