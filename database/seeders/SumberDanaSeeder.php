<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SumberDana; // 

class SumberDanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SumberDana::create(['nama_sumber_dana' => 'RM']);
        SumberDana::create(['nama_sumber_dana' => 'PNP']);
    }
}