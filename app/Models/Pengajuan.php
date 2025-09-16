<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id', // Ini sekarang untuk "Bagian"
        'tanggal_pengajuan',
        'judul',
        'deskripsi',
        'jumlah_dana',
        'status',
        'lampiran',
    ];

    /**
     * Get the user that owns the pengajuan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category (Bagian) for the pengajuan.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

