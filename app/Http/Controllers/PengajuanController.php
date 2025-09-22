<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Sumberdana;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Kro;
use App\Models\Account; 
use App\Models\Coa;
use App\Models\Pengajuan;
use App\Models\PengajuanDetail;

class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar riwayat pengajuan milik user yang sedang login.
     */

    public function getActivities($programId)
    {
        $activities = Activity::where('program_id', $programId)->get();
        return response()->json($activities);
    }


     public function getKros($activityId)
    {
        $kros = Kro::where('activity_id', $activityId)->get();
        return response()->json($kros);
    }


    public function index()
    {
        // Ambil semua pengajuan milik user yang login
        // 'with' digunakan untuk mengambil data relasi agar lebih efisien (Eager Loading)
        $pengajuans = Pengajuan::with(['details'])
            ->where('user_id', auth()->id())
            ->latest() // Urutkan dari yang paling baru
            ->get();

        return view('pengajuan.index', compact('pengajuans'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan baru.
     */
     public function create()
    {
        // Ambil data untuk dropdown dari database
        $ppkUsers = User::where('role', 'ppk')->get();
        $npwpUsers = User::where('role', 'npwp')->get();
        $sumberDanas = SumberDana::all();
        $programs = Program::all();

        // Kirim semua data ke view
        return view('pengajuan.create', compact('ppkUsers', 'npwpUsers', 'sumberDanas', 'programs'));
    }


    /**
     * Menyimpan pengajuan baru ke database.
     */
  // app/Http/Controllers/PengajuanController.php

public function store(Request $request)
{
    // 1. Validasi SEMUA input yang dibutuhkan oleh database
    $validatedData = $request->validate([
        'tanggal_pengajuan' => 'required|date|after_or_equal:today',
        'ppk_user_id' => 'required|exists:users,id',
        'npwp_user_id' => 'required|exists:users,id',
        'sumber_dana_id' => 'required|exists:sumber_danas,id',
        'uraian' => 'required|string', // <-- Pastikan ini ada
        'program_id' => 'required|exists:programs,id',
        'activity_id' => 'required|exists:activities,id',
        'kro_id' => 'required|exists:kros,id',
        'details' => 'required|array',
        'details.*.coa_id' => 'required|exists:coas,id',
        'details.*.jumlah_diajukan' => 'required|numeric|min:1',
    ]);
    
    // 2. Tambahkan data user yang login ke data utama
    $validatedData['user_id'] = auth()->id();

    // 3. Simpan data ke tabel induk (pengajuans)
    $pengajuan = Pengajuan::create($validatedData);

    // 4. Looping dan simpan data ke tabel anak (pengajuan_details)
    foreach ($request->details as $detail) {
        PengajuanDetail::create([
            'pengajuan_id' => $pengajuan->id,
            'coa_id' => $detail['coa_id'],
            'jumlah_diajukan' => $detail['jumlah_diajukan'],
        ]);
    }

    return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim dan sedang ditinjau.');
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
    // Keamanan: Pastikan user hanya bisa melihat pengajuannya sendiri,
    // sementara admin (NPWP & PPK) bisa melihat semua.
    $user = auth()->user();
    if ($user->role == 'polsek' || $user->role == 'bagian') {
        if ($pengajuan->user_id != $user->id) {
            abort(403);
        }
    }

    // Load semua relasi yang dibutuhkan agar efisien
    $pengajuan->load(['user', 'program', 'activity', 'kro', 'sumberDana', 'details.coa.account']);
    
    return view('pengajuan.show', compact('pengajuan'));
}


// METHOD BARU UNTUK MENGAMBIL AKUN BELANJA
    public function getAccounts()
    {
        $accounts = Account::orderBy('nama_akun_belanja', 'asc')->get();
        return response()->json($accounts);
    }

    // METHOD BARU UNTUK MENGAMBIL COA
    public function getCoas($accountId)
    {
        $coas = Coa::where('account_id', $accountId)->get();
        return response()->json($coas);
    }
}