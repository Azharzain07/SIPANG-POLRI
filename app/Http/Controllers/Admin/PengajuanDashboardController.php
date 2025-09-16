<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanExport;

class PengajuanDashboardController extends Controller
{
    /**
     * Menampilkan halaman review untuk Admin, lengkap dengan ringkasan data.
     */
    public function index(Request $request) // Tambahkan Request $request
{
    // Mulai query, jangan langsung get()
    $query = Pengajuan::with(['user', 'category']);

    // Logika Pencarian
    $query->when($request->search, function ($q, $search) {
        // Jika ada input 'search', tambahkan kondisi where
        return $q->where('judul', 'like', "%{$search}%");
    });

    // Eksekusi query setelah semua kondisi ditambahkan
    $pengajuans = $query->latest()->get();

    // --- Data untuk Ringkasan ---
    // (Logika ini tetap sama)
    $pendingCount = Pengajuan::where('status', 'pending')->count();
    $totalDanaDiterima = Pengajuan::where('status', 'diterima')->sum('jumlah_dana');
    $totalDanaDiajukan = Pengajuan::sum('jumlah_dana');

    return view('admin.pengajuan.index', compact(
        'pengajuans',
        'pendingCount',
        'totalDanaDiterima',
        'totalDanaDiajukan'
    ));
        // Kirim semua data (untuk tabel dan ringkasan) ke view
        return view('admin.pengajuan.index', compact(
            'pengajuans',
            'pendingCount',
            'totalDanaDiterima',
            'totalDanaDiajukan'
        ));
    }

    /**
     * Menyetujui sebuah pengajuan dan mengurangi budget user.
     */
        public function approve(Pengajuan $pengajuan)
    {
        $user = $pengajuan->user;
        $jumlahDiajukan = $pengajuan->jumlah_dana;

        // INI ADALAH LOGIKA PENGECEKANNYA
        if ($user->budget_tahunan < $jumlahDiajukan) {
            // Jika budget kurang, kembali dengan pesan error
            return back()->with('error', 'Budget user tidak mencukupi untuk menyetujui pengajuan ini.');
        }

    // Jika budget cukup, lanjutkan proses
    $user->budget_tahunan -= $jumlahDiajukan;
    $user->save();
    
    $pengajuan->update(['status' => 'diterima']);

    return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil disetujui.');
}

    /**
     * Menolak sebuah pengajuan.
     */
    public function reject(Pengajuan $pengajuan)
    {
        // Cukup ubah statusnya
        $pengajuan->update(['status' => 'ditolak']);
        
        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil ditolak.');
    }
    
    // TAMBAHKAN METHOD BARU INI
    public function exportPDF(Request $request)
    {
        // Logika query sama persis dengan method index()
        $query = Pengajuan::with(['user', 'category']);

        $query->when($request->search, function ($q, $search) {
            return $q->where('judul', 'like', "%{$search}%");
        });

        $pengajuans = $query->latest()->get();

        // Buat PDF
        $pdf = PDF::loadView('admin.pengajuan.export-pdf', compact('pengajuans'));

        // Download file PDF
        return $pdf->download('laporan-pengajuan-anggaran-' . date('Y-m-d') . '.pdf');
    }
    public function exportExcel(Request $request)
    {
        // Logika query sama persis dengan method index()
        $query = Pengajuan::with(['user', 'category']);

        $query->when($request->search, function ($q, $search) {
            return $q->where('judul', 'like', "%{$search}%");
        });

        $pengajuans = $query->latest()->get();

        // Panggil class export dan unduh filenya
        return Excel::download(new PengajuanExport($pengajuans), 'laporan-pengajuan-' . date('Y-m-d') . '.xlsx');
    }
}