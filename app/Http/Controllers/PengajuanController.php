<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar riwayat pengajuan milik user yang sedang login.
     */
    public function index()
    {
        $pengajuans = Pengajuan::with(['user', 'category'])->where('user_id', auth()->id())->latest()->get();
        $sisaBudget = auth()->user()->budget_tahunan;
        return view('pengajuan.index', compact('pengajuans', 'sisaBudget'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan baru.
     */
    public function create()
    {
        // Mengambil semua data dari tabel categories (yang sekarang berisi "Bagian")
        $bagianList = Category::orderBy('nama_kategori', 'asc')->get();
        
        return view('pengajuan.create', compact('bagianList'));
    }

    /**
     * Menyimpan pengajuan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal_pengajuan' => 'required|date|after_or_equal:today',
            'judul' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'jumlah_dana' => 'required|numeric|min:1',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran-pengajuan', 'public');
            $validatedData['lampiran'] = $path;
        }

        $validatedData['user_id'] = auth()->id();
        $validatedData['status'] = 'pending';

        Pengajuan::create($validatedData);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    /**
     * Menampilkan form untuk mengedit pengajuan.
     */
    public function edit(Pengajuan $pengajuan)
    {
        // Keamanan: Pastikan user hanya bisa edit pengajuannya sendiri & statusnya pending
        if ($pengajuan->user_id != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // Ambil daftar Bagian untuk dropdown
        $bagianList = Category::orderBy('nama_kategori', 'asc')->get();

        return view('pengajuan.edit', compact('pengajuan', 'bagianList'));
    }
    
    /**
     * Memperbarui data pengajuan di database.
     */
    public function update(Request $request, Pengajuan $pengajuan)
    {
        // Keamanan: Pastikan user hanya bisa update pengajuannya sendiri & statusnya pending
        if ($pengajuan->user_id != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $validatedData = $request->validate([
            'tanggal_pengajuan' => 'required|date|after_or_equal:today',
            'judul' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'jumlah_dana' => 'required|numeric|min:1',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Logika untuk upload file baru jika ada
        if ($request->hasFile('lampiran')) {
            // (Opsional) Tambahkan logika untuk hapus file lama jika ada
            $path = $request->file('lampiran')->store('lampiran-pengajuan', 'public');
            $validatedData['lampiran'] = $path;
        }

        $pengajuan->update($validatedData);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }
    
    /**
     * Menghapus pengajuan dari database.
     */
    public function destroy(Pengajuan $pengajuan)
    {
        // Keamanan: Pastikan user hanya bisa hapus pengajuannya sendiri & statusnya pending
        if ($pengajuan->user_id != auth()->id() || $pengajuan->status != 'pending') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }
        
        // (Opsional) Tambahkan logika untuk hapus file lampiran dari storage
        
        $pengajuan->delete();
        
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
    // app/Http/Controllers/PengajuanController.php

public function show(Pengajuan $pengajuan)
{
    // Keamanan: User hanya boleh lihat miliknya, admin boleh lihat semua
    if (auth()->user()->role != 'admin' && $pengajuan->user_id != auth()->id()) {
        abort(403, 'AKSI TIDAK DIIZINKAN.');
    }

    // Kirim data pengajuan ke view
    return view('pengajuan.show', compact('pengajuan'));
}
}