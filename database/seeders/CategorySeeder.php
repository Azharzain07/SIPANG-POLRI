<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    use App\Models\Category;

public function run()
{
    Category::create(['nama_kategori' => 'Bagian A']);
    Category::create(['nama_kategori' => 'Bagian B']);
}

}
