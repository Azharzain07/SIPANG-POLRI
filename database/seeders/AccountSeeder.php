<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        Account::create(['nama_akun_belanja' => '521 - Belanja Barang']);
        Account::create(['nama_akun_belanja' => '524 - Belanja Perjalanan Dinas']);
        Account::create(['nama_akun_belanja' => '531 - Belanja Modal']);
    }
}
