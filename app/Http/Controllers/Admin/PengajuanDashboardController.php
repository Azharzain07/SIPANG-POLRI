<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanExport;
use App\Models\PengajuanDetail; // Pastikan ini di-import

class PengajuanDashboardController extends Controller
{
    /**
     * Menampilkan halaman review untuk Admin/PPK.
     */
    public function index(Request $request)
    {
        // Query dasar dengan Eager Loading untuk efisiensi
        $query = Pengajuan::with(['user', 'details']);

        // Terapkan filter pencarian jika ada
        $query->when($request->search, function ($q, $search) {
            return $q->where('uraian', 'like', "%{$search}%");
        });

        // === INI PERBAIKANNYA: Ubah .get() menjadi .paginate() ===
        $pengajuans = $query->latest()->paginate(10); // Menampilkan 10 data per halaman

        // --- Logika Perhitungan untuk Card Ringkasan (dibuat lebih efisien) ---
        $pendingCount = Pengajuan::where('status_ppk', 'pending')
                                 ->where('status_npwp', 'diterima')
                                 ->count();
        
        $totalDanaDiterima = Pengajuan::where('status_ppk', 'diterima')
                                    ->with('details')
                                    ->get()
                                    ->sum(function($p) {
                                        return $p->details->sum('jumlah_diajukan');
                                    });

        $totalDanaDiajukan = PengajuanDetail::sum('jumlah_diajukan');

        return view('admin.pengajuan.index', compact(
            'pengajuans',
            'pendingCount',
            'totalDanaDiterima',
            'totalDanaDiajukan'
        ));
    }

    /**
     * Menyetujui pengajuan sebagai PPK.
     */
    public function approvePpk(Pengajuan $pengajuan)
    {
        // PPK hanya bisa approve jika NPWP sudah approve
        if ($pengajuan->status_npwp !== 'diterima') {
            return back()->with('error', 'Pengajuan ini belum disetujui oleh NPWP.');
        }

        $pengajuan->status_ppk = 'diterima';
        $pengajuan->ppk_processed_at = now();
        $pengajuan->save();

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil disetujui.');
    }

    /**
     * Menolak pengajuan sebagai PPK.
     */
    public function rejectPpk(Pengajuan $pengajuan)
    {
        $pengajuan->status_ppk = 'ditolak';
        $pengajuan->ppk_processed_at = now();
        $pengajuan->save();

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil ditolak.');
    }

    /**
     * Mengekspor data ke PDF.
     */
    public function exportPDF(Request $request)
    {
        // Menggunakan query yang sama dengan function index untuk konsistensi
        $query = Pengajuan::with(['user', 'details']);
        $query->when($request->search, function ($q, $search) {
            return $q->where('uraian', 'like', "%{$search}%");
        });
        $pengajuans = $query->latest()->get();

        $pdf = PDF::loadView('admin.pengajuan.export-pdf', compact('pengajuans'));
        return $pdf->download('laporan-pengajuan-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Mengekspor data ke Excel.
     */
    public function exportExcel(Request $request)
    {
        // Menggunakan query yang sama dengan function index untuk konsistensi
        $query = Pengajuan::with(['user', 'details']);
        $query->when($request->search, function ($q, $search) {
            return $q->where('uraian', 'like', "%{$search}%");
        });
        $pengajuans = $query->latest()->get();

        return Excel::download(new PengajuanExport($pengajuans), 'laporan-pengajuan-' . date('Y-m-d') . '.xlsx');
    }
}