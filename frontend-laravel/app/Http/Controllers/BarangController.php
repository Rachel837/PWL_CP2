<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarangController extends Controller
{
    public function index()
    {
        // Panggil endpoint Node.js API
        $response = Http::get('http://localhost:5000/api/barang');
        
        $barang = [];
        if ($response->successful() && isset($response->json()['data'])) {
            $barang = $response->json()['data'];
        }

        // Tampilkan view inventory dan berikan data $barang
        return view('inventory', ['barang' => $barang]);
    }
}
