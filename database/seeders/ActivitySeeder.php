<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Cari ID dari program yang sudah kita buat
        $programDukman = Program::where('nama_program', 'Program Dukungan Manajemen')->first();
        $programPenyelidikan = Program::where('nama_program', 'Program Penyelidikan Kriminal')->first();

        // Buat aktivitas yang terhubung ke Program Dukungan Manajemen
        if ($programDukman) {
            Activity::create(['program_id' => $programDukman->id, 'nama_aktivitas' => 'Pengadaan ATK dan Komputer']);
            Activity::create(['program_id' => $programDukman->id, 'nama_aktivitas' => 'Layanan Perkantoran']);
        }
        
        // Buat aktivitas yang terhubung ke Program Penyelidikan
        if ($programPenyelidikan) {
            Activity::create(['program_id' => $programPenyelidikan->id, 'nama_aktivitas' => 'Operasi Penyelidikan Lapangan']);
        }
    }
}