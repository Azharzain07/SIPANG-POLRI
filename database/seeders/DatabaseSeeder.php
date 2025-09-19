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
            AccountSeeder::class,
            // Anda bisa tambahkan ActivitySeeder, KroSeeder, CoaSeeder di sini
            UserSeeder::class,
        ]);
    }
}
