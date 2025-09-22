<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        UserSeeder::class,
        SumberDanaSeeder::class,
        ProgramSeeder::class,
        ActivitySeeder::class,
        KroSeeder::class,
        AccountSeeder::class, 
        // CoaSeeder::class,    // <-- HAPUS ATAU BERI KOMENTAR BARIS INI
    ]);
    }
}