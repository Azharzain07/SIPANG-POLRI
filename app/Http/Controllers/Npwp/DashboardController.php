<?php

namespace App\Http\Controllers\Npwp;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil hanya pengajuan yang status NPWP-nya masih 'pending'
        $pengajuans = Pengajuan::with('user')
            ->where('status_npwp', 'pending')
            ->latest()
            ->get();
            
        return view('npwp.dashboard', compact('pengajuans'));
    }
    public function approve(Pengajuan $pengajuan)
    {
        $pengajuan->update([
            'status_npwp' => 'diterima',
            'npwp_user_id' => auth()->id(), // Mencatat siapa yang menyetujui
            'npwp_processed_at' => now(),    // Mencatat kapan disetujui
        ]);

        return redirect()->route('npwp.dashboard')->with('success', 'Pengajuan berhasil disetujui dan diteruskan ke PPK.');
    }

    // METHOD BARU UNTUK MENOLAK
    public function reject(Pengajuan $pengajuan)
    {
        $pengajuan->update([
            'status_npwp' => 'ditolak',
            'npwp_user_id' => auth()->id(),
            'npwp_processed_at' => now(),
        ]);

        return redirect()->route('npwp.dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }
}