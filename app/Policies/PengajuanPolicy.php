<?php

namespace App\Policies;

use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengajuanPolicy
{
    /**
     * Memberikan akses super-admin kepada role tertentu sebelum pengecekan lainnya.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Jika user adalah 'admin' atau 'ppk', berikan akses penuh untuk semua aksi
        if (in_array($user->role, ['admin', 'ppk'])) {
            return true;
        }

        return null; // Lanjutkan ke pemeriksaan policy jika bukan super-admin
    }

    /**
     * Tentukan apakah pengguna boleh MELIHAT detail pengajuan.
     */
    public function view(User $user, Pengajuan $pengajuan): bool
    {
        // Izinkan jika pengguna adalah PEMILIK pengajuan
        // ATAU jika role pengguna adalah 'npwp' (yang perlu melihat untuk approve tahap 1)
        return $user->id === $pengajuan->user_id || $user->role === 'npwp';
    }

    /**
     * Tentukan apakah pengguna boleh MENGEDIT/UPDATE pengajuan.
     */
    public function update(User $user, Pengajuan $pengajuan): bool
    {
        // Hanya PEMILIK yang boleh mengedit
        return $user->id === $pengajuan->user_id;
    }

    /**
     * Tentukan apakah pengguna boleh MENGHAPUS pengajuan.
     */
    public function delete(User $user, Pengajuan $pengajuan): bool
    {
        // Hanya PEMILIK yang boleh menghapus
        return $user->id === $pengajuan->user_id;
    }
}