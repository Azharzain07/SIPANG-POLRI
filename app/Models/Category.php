<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'parent_id',
    ];

    // Relasi ke induk (satu) - SUDAH DIBUAT
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // TAMBAHKAN RELASI KE ANAK (BANYAK)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}