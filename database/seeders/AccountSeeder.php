<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Coa;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Akun Belanja dan simpan hasilnya di variabel
        $belanjaBarang = Account::create(['nama_akun_belanja' => '521 - Belanja Barang']);
        $belanjaModal = Account::create(['nama_akun_belanja' => '531 - Belanja Modal']);

        // Buat COA untuk Belanja Barang, gunakan ID dari variabel di atas
        if ($belanjaBarang) {
            Coa::create(['account_id' => $belanjaBarang->id, 'nama_coa' => 'ATK dan Komputer', 'pagu' => 50000000, 'sisa_pagu' => 50000000]);
            Coa::create(['account_id' => $belanjaBarang->id, 'nama_coa' => 'Langganan Daya dan Jasa', 'pagu' => 100000000, 'sisa_pagu' => 100000000]);
        }
        
        // Buat COA untuk Belanja Modal
        if ($belanjaModal) {
            Coa::create(['account_id' => $belanjaModal->id, 'nama_coa' => 'Peralatan dan Mesin', 'pagu' => 250000000, 'sisa_pagu' => 250000000]);
        }
    }
}
