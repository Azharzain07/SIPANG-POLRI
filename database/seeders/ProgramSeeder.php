<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::create(['nama_program' => 'Program Dukungan Manajemen']);
        Program::create(['nama_program' => 'Program Penyelidikan & Penyidikan Tindak Pidana']);
    }
}
