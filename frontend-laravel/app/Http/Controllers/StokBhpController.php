<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StokBhpController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->apiUrl}/stok-bhp");
        $stokBhp = $response->json('data') ?? [];
        return view('stokbhp.index', compact('stokBhp'));
    }

    public function create()
    {
        $response = Http::get("{$this->apiUrl}/stok-bhp/available-items");
        $availableItems = $response->json('data') ?? [];
        return view('stokbhp.form', compact('availableItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|numeric',
            'jumlah_stok' => 'required|numeric|min:0',
            'minimal_stok' => 'required|numeric|min:0',
        ]);

        $response = Http::post("{$this->apiUrl}/stok-bhp", $request->all());

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal menyimpan stok BHP.')->withInput();
        }

        return redirect()->route('stok-bhp.index')->with('success', 'Stok BHP berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $response = Http::get("{$this->apiUrl}/stok-bhp");
        $stocks = $response->json('data') ?? [];
        
        $stok = null;
        foreach ($stocks as $s) {
            if ($s['id'] == $id) {
                $stok = $s;
                break;
            }
        }

        if (!$stok) {
            return redirect()->route('stok-bhp.index')->with('error', 'Stok BHP tidak ditemukan.');
        }

        return view('stokbhp.form', compact('stok'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_stok' => 'required|numeric|min:0',
            'minimal_stok' => 'required|numeric|min:0',
        ]);

        $response = Http::put("{$this->apiUrl}/stok-bhp/{$id}", $request->all());

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal mengupdate stok BHP.')->withInput();
        }

        return redirect()->route('stok-bhp.index')->with('success', 'Stok BHP berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $response = Http::delete("{$this->apiUrl}/stok-bhp/{$id}");

        if ($response->failed()) {
            return redirect()->route('stok-bhp.index')->with('error', $response->json('message') ?? 'Gagal menghapus stok BHP.');
        }

        return redirect()->route('stok-bhp.index')->with('success', 'Stok BHP berhasil dihapus.');
    }
}
