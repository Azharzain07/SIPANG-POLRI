<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Kro;

class KroSeeder extends Seeder
{
    public function run(): void
    {
        // Cari ID dari aktivitas yang sudah kita buat
        $aktivitasAtk = Activity::where('nama_aktivitas', 'Pengadaan ATK dan Komputer')->first();
        $aktivitasOps = Activity::where('nama_aktivitas', 'Operasi Penyelidikan Lapangan')->first();

        // Buat KRO yang terhubung ke Aktivitas ATK
        if ($aktivitasAtk) {
            Kro::create(['activity_id' => $aktivitasAtk->id, 'nama_kro' => 'KRO-A1: Layanan Dukungan Perangkat', 'lokasi' => 'Internal Polres Garut']);
            Kro::create(['activity_id' => $aktivitasAtk->id, 'nama_kro' => 'KRO-A2: Pemeliharaan Sistem', 'lokasi' => 'Kantor TI']);
        }

        // Buat KRO yang terhubung ke Aktivitas Operasi
        if ($aktivitasOps) {
            Kro::create(['activity_id' => $aktivitasOps->id, 'nama_kro' => 'KRO-B1: Penyelidikan Kasus A', 'lokasi' => 'Wilayah Garut Kota']);
        }
    }
}