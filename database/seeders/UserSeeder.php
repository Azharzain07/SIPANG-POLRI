<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Polsek
        User::create(['name' => 'User Polsek Garut Kota', 'nama_polsek' => 'POLSEK GARUT KOTA', 'email' => 'polsek@contoh.com', 'password' => Hash::make('password'), 'role' => 'polsek']);
        
        // User Bagian
        User::create(['name' => 'User Bagian SDM', 'nama_polsek' => 'BAGIAN SDM', 'email' => 'bagian@contoh.com', 'password' => Hash::make('password'), 'role' => 'bagian']);

        // User NPWP (Admin Kecil)
        User::create(['name' => 'Admin NPWP', 'email' => 'npwp@contoh.com', 'password' => Hash::make('password'), 'role' => 'npwp']);

        // User PPK (Admin Besar)
        User::create(['name' => 'Admin PPK', 'email' => 'ppk@contoh.com', 'password' => Hash::make('password'), 'role' => 'ppk']);
    }
}
