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
            $userId = session('user.id');
            
            // Get draft pengadaan from API
            $response = Http::get("{$this->apiUrl}/draft-pengadaan/user/{$userId}");
            
            if ($response->successful()) {
                $draftPengadaans = $response->json('data') ?? [];
                return view('draftpengadaan.index', compact('draftPengadaans'));
            }
            
            return back()->with('error', 'Gagal mengambil data draft pengadaan');
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
            ]);

            $userId = session('user.id');

            $response = Http::post("{$this->apiUrl}/draft-pengadaan", [
                'tahun' => $request->tahun,
                'users_id' => $userId,
                'catatan' => $request->catatan,
            ]);

            if ($response->successful()) {
                $draftPengadaan = $response->json('data');
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
            $userId = session('user.id');
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
            $userId = session('user.id');
            if ($draftPengadaan['users_id'] != $userId) {
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
                'status' => 'submitted',
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Draft pengadaan berhasil diajukan untuk approval');
            }

            return back()->with('error', 'Gagal mengajukan draft pengadaan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
