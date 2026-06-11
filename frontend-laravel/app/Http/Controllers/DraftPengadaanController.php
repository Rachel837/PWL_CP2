<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DraftPengadaanController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:5000/api');
    }

    /**
     * Display a listing of draft pengadaan
     */
    public function index(Request $request)
    {
        try {
            $user = session('user');
            
            // Get draft pengadaan from API
            if ($user['role'] === 'kepala laboratorium') {
                $response = Http::get("{$this->apiUrl}/draft-pengadaan/user/{$user['id']}");
            } elseif ($user['role'] === 'staf administrasi') {
                $response = Http::get("{$this->apiUrl}/draft-pengadaan", [
                    'status' => 'disetujui'
                ]);
            } else {
                $response = Http::get("{$this->apiUrl}/draft-pengadaan");
            }
            
            if ($response->successful()) {
                $allDrafts = $response->json('data') ?? [];
                $draftPengadaans = $allDrafts;
                
                return view('draftpengadaan.index', compact('draftPengadaans'));
            }
            
            return back()->with('error', 'Gagal mengambil data draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of draft pengadaan khusus untuk penerimaan barang
     */
    public function penerimaanIndex(Request $request)
    {
        try {
            // Get draft pengadaan from API that are approved
            $response = Http::get("{$this->apiUrl}/draft-pengadaan", [
                'status' => 'disetujui'
            ]);
            
            if ($response->successful()) {
                $allDrafts = $response->json('data') ?? [];
                $draftPengadaans = $allDrafts;
                
                $isPenerimaan = true;
                return view('draftpengadaan.index', compact('draftPengadaans', 'isPenerimaan'));
            }
            
            return back()->with('error', 'Gagal mengambil data draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of submitted draft pengadaan for history
     */
    public function history(Request $request)
    {
        try {
            $user = session('user');
            
            if ($user['role'] === 'ketua program studi') {
                // Get draft pengadaan from API with status disetujui
                $response = Http::get("{$this->apiUrl}/draft-pengadaan", [
                    'status' => 'disetujui'
                ]);
                
                if ($response->successful()) {
                    $historyDrafts = $response->json('data') ?? [];
                    return view('draftpengadaan.history', compact('historyDrafts'));
                }
            } else {
                $userId = $user['id'];
                
                // Get draft pengadaan from API for kalab
                $response = Http::get("{$this->apiUrl}/draft-pengadaan/user/{$userId}");
                
                if ($response->successful()) {
                    $allDrafts = $response->json('data') ?? [];
                    // Filter only drafts that are in 'disetujui' status
                    $historyDrafts = array_filter($allDrafts, function($draft) {
                        return $draft['status'] === 'disetujui';
                    });
                    return view('draftpengadaan.history', compact('historyDrafts'));
                }
            }
            
            return back()->with('error', 'Gagal mengambil data history pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new draft pengadaan
     */
    public function create(Request $request)
    {
        try {
            // Get available items from API
            $barangResponse = Http::get("{$this->apiUrl}/barang-tersedia");
            
            $barang = [];
            if ($barangResponse->successful()) {
                $barang = $barangResponse->json('data') ?? [];
            }
            
            return view('draftpengadaan.create', compact('barang'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created draft pengadaan in storage
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun' => 'required|string',
                'catatan' => 'nullable|string',
                'barang_id' => 'nullable|numeric',
                'jumlah' => 'nullable|numeric|min:1',
                'harga_estimasi' => 'nullable|numeric|min:0',
                'link_pembelian' => 'nullable|url',
                'inventaris_id_lama' => 'nullable|numeric',
            ]);

            $userId = session('user')['id'];

            $response = Http::post("{$this->apiUrl}/draft-pengadaan", [
                'tahun' => $request->tahun,
                'users_id' => $userId,
                'catatan' => $request->catatan,
            ]);

            if ($response->successful()) {
                $draftPengadaan = $response->json('data');
                
                if ($request->filled('barang_id') && $request->filled('jumlah')) {
                    Http::post("{$this->apiUrl}/draft-pengadaan-detail", [
                        'draft_pengadaan_id' => $draftPengadaan['id'],
                        'barang_id' => $request->barang_id,
                        'jumlah' => $request->jumlah,
                        'harga_estimasi' => $request->harga_estimasi ?? 0,
                        'link_pembelian' => $request->link_pembelian,
                        'inventaris_id_lama' => $request->inventaris_id_lama ?: null,
                    ]);
                }

                return redirect()->route('draft-pengadaan.edit', $draftPengadaan['id'])
                    ->with('success', 'Draft pengadaan berhasil dibuat');
            }

            return back()->with('error', 'Gagal membuat draft pengadaan')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified draft pengadaan
     */
    public function edit($id)
    {
        try {
            // Get draft pengadaan details
            $response = Http::get("{$this->apiUrl}/draft-pengadaan/{$id}");
            
            if (!$response->successful()) {
                return back()->with('error', 'Draft pengadaan tidak ditemukan');
            }

            $draftPengadaan = $response->json('data');

            // Get available items
            $barangResponse = Http::get("{$this->apiUrl}/barang-tersedia");
            $barang = $barangResponse->successful() ? $barangResponse->json('data') ?? [] : [];

            // Check authorization
            $userId = session('user')['id'];
            if ($draftPengadaan['users_id'] != $userId) {
                return back()->with('error', 'Anda tidak memiliki akses ke draft ini');
            }

            return view('draftpengadaan.edit', compact('draftPengadaan', 'barang'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Add item detail to draft pengadaan
     */
    public function addDetail(Request $request)
    {
        try {
            $request->validate([
                'draft_pengadaan_id' => 'required|numeric',
                'barang_id' => 'required|numeric',
                'jumlah' => 'required|numeric|min:1',
                'harga_estimasi' => 'required|numeric|min:0',
                'link_pembelian' => 'nullable|url',
                'inventaris_id_lama' => 'nullable|numeric',
            ]);

            $response = Http::post("{$this->apiUrl}/draft-pengadaan-detail", [
                'draft_pengadaan_id' => $request->draft_pengadaan_id,
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_estimasi' => $request->harga_estimasi,
                'link_pembelian' => $request->link_pembelian,
                'inventaris_id_lama' => $request->inventaris_id_lama ?: null,
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Barang berhasil ditambahkan ke draft');
            }

            return back()->with('error', 'Gagal menambahkan barang')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Update item detail in draft pengadaan
     */
    public function updateDetail(Request $request, $detailId)
    {
        try {
            $request->validate([
                'jumlah' => 'required|numeric|min:1',
                'harga_estimasi' => 'required|numeric|min:0',
                'link_pembelian' => 'nullable|url',
                'inventaris_id_lama' => 'nullable|numeric',
            ]);

            $response = Http::put("{$this->apiUrl}/draft-pengadaan-detail/{$detailId}", [
                'jumlah' => $request->jumlah,
                'harga_estimasi' => $request->harga_estimasi,
                'link_pembelian' => $request->link_pembelian,
                'inventaris_id_lama' => $request->inventaris_id_lama ?: null,
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Detail barang berhasil diupdate');
            }

            return back()->with('error', 'Gagal mengupdate detail barang')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove item detail from draft pengadaan
     */
    public function deleteDetail($detailId)
    {
        try {
            $response = Http::delete("{$this->apiUrl}/draft-pengadaan-detail/{$detailId}");

            if ($response->successful()) {
                return back()->with('success', 'Barang berhasil dihapus dari draft');
            }

            return back()->with('error', 'Gagal menghapus barang');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the specified draft pengadaan
     */
    public function show($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/draft-pengadaan/{$id}");
            
            if (!$response->successful()) {
                return back()->with('error', 'Draft pengadaan tidak ditemukan');
            }

            $draftPengadaan = $response->json('data');

            // Check authorization
            $userId = session('user')['id'];
            $userRole = session('user')['role'] ?? '';
            $hasAccess = false;

            if ($draftPengadaan['users_id'] == $userId) {
                $hasAccess = true;
            } elseif ($userRole === 'staf administrasi' && $draftPengadaan['status'] === 'disetujui') {
                $hasAccess = true;
            } elseif ($userRole === 'ketua program studi') {
                $hasAccess = true;
            }

            if (!$hasAccess) {
                return back()->with('error', 'Anda tidak memiliki akses ke draft ini');
            }

            return view('draftpengadaan.show', compact('draftPengadaan'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete the specified draft pengadaan
     */
    public function destroy($id)
    {
        try {
            $response = Http::delete("{$this->apiUrl}/draft-pengadaan/{$id}");

            if ($response->successful()) {
                return redirect()->route('draft-pengadaan.index')
                    ->with('success', 'Draft pengadaan berhasil dihapus');
            }

            return back()->with('error', 'Gagal menghapus draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get replacement inventories for a specific item
     */
    public function getReplacementInventaris($barangId)
    {
        try {
            $response = Http::get("{$this->apiUrl}/inventaris-pengganti/{$barangId}");

            if ($response->successful()) {
                return response()->json($response->json('data') ?? []);
            }

            return response()->json([], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit draft pengadaan for approval
     */
    public function submit($id)
    {
        try {
            $response = Http::put("{$this->apiUrl}/draft-pengadaan/{$id}", [
                'status' => 'diajukan',
            ]);

            if ($response->successful()) {
                return redirect()->route('draft-pengadaan.index')->with('success', 'Draft pengadaan berhasil diajukan untuk direview');
            }

            return back()->with('error', 'Gagal mengajukan draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of reviewed drafts for Kaprodi review
     */
    public function reviewIndex(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        
        try {
            $response = Http::get("{$this->apiUrl}/draft-pengadaan", [
                'status' => $status
            ]);
            
            if ($response->successful()) {
                $draftPengadaans = $response->json('data') ?? [];
                return view('draftpengadaan.review.index', compact('draftPengadaans'));
            }
            
            return back()->with('error', 'Gagal mengambil data draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the specified draft pengadaan for Kaprodi review
     */
    public function reviewShow($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/draft-pengadaan/{$id}");
            
            if (!$response->successful()) {
                return back()->with('error', 'Draft pengadaan tidak ditemukan');
            }

            $draftPengadaan = $response->json('data');

            return view('draftpengadaan.review.show', compact('draftPengadaan'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update item detail status (approve/reject) in draft pengadaan
     */
    public function reviewUpdateDetail(Request $request, $id, $detailId)
    {
        try {
            $request->validate([
                'status_approval' => 'required|in:disetujui,ditolak,pending',
                'catatan_kaprodi' => 'nullable|string',
            ]);

            $response = Http::put("{$this->apiUrl}/draft-pengadaan-detail/{$detailId}", [
                'status_approval' => $request->status_approval,
                'catatan_kaprodi' => $request->catatan_kaprodi,
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Status barang berhasil diupdate');
            }

            return back()->with('error', 'Gagal mengupdate status barang')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Finalize the draft pengadaan
     */
    public function reviewFinalize(Request $request, $id)
    {
        try {
            // Get the draft details to determine final status
            $draftResponse = Http::get("{$this->apiUrl}/draft-pengadaan/{$id}");
            if (!$draftResponse->successful()) {
                return back()->with('error', 'Gagal memuat draf untuk finalisasi');
            }
            $draft = $draftResponse->json('data');
            
            $hasApproved = false;
            foreach($draft['details'] ?? [] as $detail) {
                if ($detail['status_approval'] === 'disetujui') {
                    $hasApproved = true;
                    break;
                }
            }
            
            $finalStatus = $hasApproved ? 'disetujui' : 'ditolak';

            $response = Http::put("{$this->apiUrl}/draft-pengadaan/{$id}", [
                'status' => $finalStatus,
            ]);

            if ($response->successful()) {
                return redirect()->route('draft-pengadaan.review.index')->with('success', 'Draft pengadaan berhasil difinalisasi dengan status: ' . ucfirst($finalStatus));
            }

            return back()->with('error', 'Gagal memfinalisasi draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan form penerimaan barang untuk staf administrasi
     */
    public function prosesPenerimaan($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/draft-pengadaan/{$id}");
            
            if (!$response->successful()) {
                return back()->with('error', 'Draft pengadaan tidak ditemukan');
            }

            $draftPengadaan = $response->json('data');

            // Ambil data ruangan untuk pilihan penempatan
            $ruanganResponse = Http::get("{$this->apiUrl}/ruangan");
            $ruangan = $ruanganResponse->successful() ? $ruanganResponse->json('data') ?? [] : [];

            return view('draftpengadaan.terima-barang', compact('draftPengadaan', 'ruangan'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Simpan data penerimaan barang ke inventaris
     */
    public function storePenerimaan(Request $request, $id)
    {
        try {
            $request->validate([
                'draft_pengadaan_detail_id' => 'required|numeric',
                'items' => 'required|array|min:1',
                'items.*.kode_inventaris' => 'nullable|string',
                'items.*.qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'items.*.tanggal_masuk' => 'nullable|date',
                'items.*.ruangan_id' => 'nullable|numeric',
            ]);

            $itemsData = [];
            foreach ($request->input('items', []) as $i => $item) {
                $qrPath = null;
                if ($request->hasFile("items.{$i}.qr_code")) {
                    $file = $request->file("items.{$i}.qr_code");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Pastikan direktori tujuan ada
                    $targetDir = public_path('uploads/qr_codes');
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    
                    $file->move($targetDir, $filename);
                    $qrPath = "/uploads/qr_codes/{$filename}";
                }
                
                $itemsData[] = [
                    'kode_inventaris' => $item['kode_inventaris'] ?? null,
                    'qr_code' => $qrPath,
                    'tanggal_masuk' => $item['tanggal_masuk'] ?? null,
                    'ruangan_id' => $item['ruangan_id'] ?? null,
                ];
            }

            $response = Http::post("{$this->apiUrl}/draft-pengadaan/terima-barang", [
                'draft_pengadaan_detail_id' => $request->draft_pengadaan_detail_id,
                'items' => $itemsData
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Barang berhasil diterima dan dicatat ke inventaris');
            }

            return back()->with('error', $response->json('message') ?? 'Gagal menerima barang');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
