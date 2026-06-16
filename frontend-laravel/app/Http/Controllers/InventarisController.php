<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InventarisController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    /**
     * Check if user is logged in and has one of the allowed roles.
     */
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkRoles(['kepala laboratorium', 'ketua program studi', 'staf administrasi', 'staf laboratorium']);

        $response = Http::get("{$this->apiUrl}/inventaris");
        $inventaris = $response->json('data') ?? [];

        // Fetch all ruangan for filter dropdown
        $ruanganResponse = Http::get("{$this->apiUrl}/ruangan");
        $ruangan = $ruanganResponse->json('data') ?? [];

        // Apply room filter if present
        $ruanganId = $request->input('ruangan_id');
        if ($ruanganId) {
            $inventaris = array_filter($inventaris, function($item) use ($ruanganId) {
                return ($item['ruangan_id'] ?? null) == $ruanganId;
            });
        }

        return view('inventaris.index', compact('inventaris', 'ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);

        // Fetch available barang and ruangan for dropdowns
        $barangResponse = Http::get("{$this->apiUrl}/barang");
        $barang = $barangResponse->json('data') ?? [];

        $ruanganResponse = Http::get("{$this->apiUrl}/ruangan");
        $ruangan = $ruanganResponse->json('data') ?? [];

        return view('inventaris.form', compact('barang', 'ruangan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);

        $request->validate([
            'barang_id' => 'required|numeric',
            'ruangan_id' => 'nullable|numeric',
            'kode_inventaris' => 'required|string',
            'kondisi' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'qr_code' => 'nullable|string',
            'status_barang' => 'required|string',
            'status_inventaris' => 'required|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $payload = $request->except(['foto_barang']);

        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/inventaris'), $filename);
            $payload['foto_barang'] = 'uploads/inventaris/' . $filename;
        }

        $response = Http::post("{$this->apiUrl}/inventaris", $payload);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal membuat item inventaris.')->withInput();
        }

        return redirect()->route('inventaris.index')->with('success', 'Item inventaris berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);

        $response = Http::get("{$this->apiUrl}/inventaris/{$id}");
        if ($response->failed()) {
            return redirect()->route('inventaris.index')->with('error', 'Item inventaris tidak ditemukan.');
        }
        $inventaris = $response->json('data');

        $barangResponse = Http::get("{$this->apiUrl}/barang");
        $barang = $barangResponse->json('data') ?? [];

        $ruanganResponse = Http::get("{$this->apiUrl}/ruangan");
        $ruangan = $ruanganResponse->json('data') ?? [];

        return view('inventaris.form', compact('inventaris', 'barang', 'ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);

        $request->validate([
            'barang_id' => 'required|numeric',
            'ruangan_id' => 'nullable|numeric',
            'kode_inventaris' => 'required|string',
            'kondisi' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'qr_code' => 'nullable|string',
            'status_barang' => 'required|string',
            'status_inventaris' => 'required|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $payload = $request->except(['foto_barang', '_method', '_token']);

        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/inventaris'), $filename);
            $payload['foto_barang'] = 'uploads/inventaris/' . $filename;
        }

        $response = Http::put("{$this->apiUrl}/inventaris/{$id}", $payload);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal memperbarui item inventaris.')->withInput();
        }

        return redirect()->route('inventaris.index')->with('success', 'Item inventaris berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->checkRoles(['staf administrasi', 'staf laboratorium']);

        $response = Http::delete("{$this->apiUrl}/inventaris/{$id}");

        if ($response->failed()) {
            return redirect()->route('inventaris.index')->with('error', $response->json('message') ?? 'Gagal menghapus item inventaris.');
        }

        return redirect()->route('inventaris.index')->with('success', 'Item inventaris berhasil dihapus.');
    }

    /**
     * Show upload condition form (for Staf Lab)
     */
    public function showUploadKondisi($id)
    {
        $this->checkRoles(['staf laboratorium']);

        $response = Http::get("{$this->apiUrl}/inventaris/{$id}");
        if ($response->failed()) {
            return redirect()->route('inventaris.index')->with('error', 'Item inventaris tidak ditemukan.');
        }
        $inventaris = $response->json('data');

        return view('inventaris.upload_kondisi', compact('inventaris'));
    }

    /**
     * Process upload condition (for Staf Lab)
     */
    public function uploadKondisi(Request $request, $id)
    {
        $this->checkRoles(['staf laboratorium']);

        $request->validate([
            'kondisi_pending' => 'required|string',
            'foto_pending' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $payload = [
            'kondisi_pending' => $request->kondisi_pending,
            'status_verifikasi' => 'pending'
        ];

        if ($request->hasFile('foto_pending')) {
            $file = $request->file('foto_pending');
            $filename = 'pending_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/inventaris'), $filename);
            $payload['foto_pending'] = 'uploads/inventaris/' . $filename;
        }

        $response = Http::put("{$this->apiUrl}/inventaris/{$id}", $payload);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal mengunggah laporan kondisi barang.')->withInput();
        }

        return redirect()->route('inventaris.index')->with('success', 'Laporan kondisi barang berhasil diunggah dan menunggu verifikasi.');
    }

    /**
     * Process verification of condition report (for Staf Admin)
     */
    public function verifikasiKondisi(Request $request, $id)
    {
        $this->checkRoles(['staf administrasi']);

        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $itemResponse = Http::get("{$this->apiUrl}/inventaris/{$id}");
        if ($itemResponse->failed()) {
            return redirect()->route('inventaris.index')->with('error', 'Item inventaris tidak ditemukan.');
        }
        $item = $itemResponse->json('data');

        if ($request->action === 'approve') {
            $payload = [
                'kondisi' => $item['kondisi_pending'],
                'foto_barang' => $item['foto_pending'],
                'kondisi_pending' => null,
                'foto_pending' => null,
                'status_verifikasi' => 'terverifikasi'
            ];
            $message = 'Laporan kondisi barang disetujui dan data inventaris diperbarui.';
        } else {
            $payload = [
                'kondisi_pending' => null,
                'foto_pending' => null,
                'status_verifikasi' => 'ditolak'
            ];
            $message = 'Laporan kondisi barang ditolak.';
        }

        $response = Http::put("{$this->apiUrl}/inventaris/{$id}", $payload);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Gagal melakukan verifikasi.');
        }

        return redirect()->route('inventaris.index')->with('success', $message);
    }
}
