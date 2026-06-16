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

    private function checkRoles(array $allowedRoles)
    {
        $user = Session::get('user');
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        if (!in_array($user['role'] ?? '', $allowedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $user;
    }

    public function index()
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);
        $response = Http::get("{$this->apiUrl}/maintenance");
        $maintenances = $response->json('data') ?? [];
        return view('maintenance.index', compact('maintenances'));
    }

    public function create()
    {
        $this->checkRoles(['staf laboratorium']);
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
        $this->checkRoles(['staf laboratorium']);
        $request->validate([
            'inventaris_id' => 'required|numeric',
            'kondisi_sesudah' => 'required|string',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'foto_before' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'foto_after' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Handle file uploads
        if ($request->hasFile('foto_before')) {
            $fileBefore = $request->file('foto_before');
            $filenameBefore = 'maint_before_' . time() . '_' . uniqid() . '.' . $fileBefore->getClientOriginalExtension();
            $fileBefore->move(public_path('uploads/maintenance'), $filenameBefore);
            $payload['foto_before'] = 'uploads/maintenance/' . $filenameBefore;
        }

        if ($request->hasFile('foto_after')) {
            $fileAfter = $request->file('foto_after');
            $filenameAfter = 'maint_after_' . time() . '_' . uniqid() . '.' . $fileAfter->getClientOriginalExtension();
            $fileAfter->move(public_path('uploads/maintenance'), $filenameAfter);
            $payload['foto_after'] = 'uploads/maintenance/' . $filenameAfter;
        }

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
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);
        $response = Http::get("{$this->apiUrl}/maintenance/{$id}");
        
        if ($response->failed()) {
            return redirect()->route('maintenance.index')->with('error', 'Log maintenance tidak ditemukan.');
        }

        $maintenance = $response->json('data');
        return view('maintenance.show', compact('maintenance'));
    }
}
