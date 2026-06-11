<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MaintenanceController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    public function index()
    {
        $response = Http::get("{$this->apiUrl}/maintenance");
        $maintenances = $response->json('data') ?? [];
        return view('maintenance.index', compact('maintenances'));
    }

    public function create()
    {
        // Ambil data inventaris aktif
        $inventarisResponse = Http::get("{$this->apiUrl}/inventaris");
        $inventarisList = $inventarisResponse->json('data') ?? [];

        // Ambil data stok BHP yang tersedia
        $bhpResponse = Http::get("{$this->apiUrl}/stok-bhp");
        $bhpList = $bhpResponse->json('data') ?? [];

        return view('maintenance.create', compact('inventarisList', 'bhpList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventaris_id' => 'required|numeric',
            'kondisi_sesudah' => 'required|string',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'bhps' => 'nullable|array',
            'bhps.*.bhp_id' => 'required_with:bhps|numeric',
            'bhps.*.jumlah_digunakan' => 'required_with:bhps|numeric|min:1',
        ]);

        $user = Session::get('user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Format data yang dikirim ke backend
        $payload = [
            'tanggal_maintenance' => date('Y-m-d'),
            'kondisi_sebelum' => $request->kondisi_sebelum, // Opsional, backend bisa set default jika kosong
            'kondisi_sesudah' => $request->kondisi_sesudah,
            'tindakan' => $request->tindakan ?? '',
            'catatan' => $request->catatan ?? '',
            'inventaris_id' => $request->inventaris_id,
            'users_id' => $user['id'],
            'bhps' => []
        ];

        // Filter BHP jika ada yang digunakan
        if ($request->has('bhps')) {
            foreach ($request->bhps as $item) {
                if (!empty($item['bhp_id']) && !empty($item['jumlah_digunakan'])) {
                    $payload['bhps'][] = [
                        'bhp_id' => intval($item['bhp_id']),
                        'jumlah_digunakan' => intval($item['jumlah_digunakan'])
                    ];
                }
            }
        }

        $response = Http::post("{$this->apiUrl}/maintenance", $payload);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal mencatat maintenance.')->withInput();
        }

        return redirect()->route('maintenance.index')->with('success', 'Log maintenance berhasil dicatat.');
    }

    public function show($id)
    {
        $response = Http::get("{$this->apiUrl}/maintenance/{$id}");
        
        if ($response->failed()) {
            return redirect()->route('maintenance.index')->with('error', 'Log maintenance tidak ditemukan.');
        }

        $maintenance = $response->json('data');
        return view('maintenance.show', compact('maintenance'));
    }
}
