<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pengajuans = Pengajuan::with('user')
            ->where('status_npwp', 'diterima')
            ->where('status_ppk', 'pending')
            ->latest()
            ->get();
            
        return view('ppk.dashboard', compact('pengajuans'));
    }

    /**
     * Menyetujui pengajuan final dan mengurangi pagu.
     */
    public function approve(Pengajuan $pengajuan)
    {
        try {
            // Gunakan DB Transaction untuk memastikan semua proses berhasil atau tidak sama sekali
            DB::transaction(function () use ($pengajuan) {
                // 1. Kurangi sisa pagu untuk setiap item rincian
                foreach ($pengajuan->details as $detail) {
                    $coa = Coa::find($detail->coa_id);
                    
                    // Pastikan sisa pagu masih mencukupi sebelum dikurangi
                    if ($coa && $coa->sisa_pagu >= $detail->jumlah_diajukan) {
                        $coa->sisa_pagu -= $detail->jumlah_diajukan;
                        $coa->save();
                    } else {
                        // Jika pagu tidak cukup, batalkan semua proses
                        throw new \Exception('Sisa pagu untuk COA "' . ($coa->nama_coa ?? 'N/A') . '" tidak mencukupi.');
                    }
                }

                // 2. Update status pengajuan
                $pengajuan->update([
                    'status_ppk' => 'diterima',
                    'ppk_user_id' => auth()->id(),
                    'ppk_processed_at' => now(),
                ]);
            });

            return redirect()->route('ppk.dashboard')->with('success', 'Pengajuan berhasil disetujui dan pagu telah dikurangi.');

        } catch (\Exception $e) {
            // Jika terjadi error di tengah jalan, kirim pesan error
            return redirect()->route('ppk.dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Menolak pengajuan final.
     */
    public function reject(Pengajuan $pengajuan)
    {
        $pengajuan->update([
            'status_ppk' => 'ditolak',
            'ppk_user_id' => auth()->id(),
            'ppk_processed_at' => now(),
        ]);

        return redirect()->route('ppk.dashboard')->with('success', 'Pengajuan berhasil ditolak.');
    }
}