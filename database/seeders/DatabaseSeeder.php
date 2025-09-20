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
        SumberDanaSeeder::class,
        ProgramSeeder::class,
        ActivitySeeder::class, // <-- Pastikan ini ada
        KroSeeder::class,      // <-- Pastikan ini ada
        AccountSeeder::class,
        UserSeeder::class,
        // CoaSeeder bisa ditambahkan di sini
    ]);
    }
}