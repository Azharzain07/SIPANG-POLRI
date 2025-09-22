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
        'tanggal_pengajuan',
        'status_npwp',
        'npwp_user_id',
        'npwp_processed_at',
        'status_ppk',
        'ppk_user_id',
        'ppk_processed_at',
        'uraian',
        'kppn',
        'sumber_dana_id',
        'program_id',
        'activity_id',
        'kro_id',
    ];

    // Definisikan relasi-relasi ke tabel lain
    public function user() { return $this->belongsTo(User::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function activity() { return $this->belongsTo(Activity::class); }
    public function kro() { return $this->belongsTo(Kro::class); }
    public function sumberDana() { return $this->belongsTo(SumberDana::class); }
    public function details() { return $this->hasMany(PengajuanDetail::class); }

    /**
     * Get the category (Bagian) for the pengajuan.
     * INI ADALAH METHOD YANG HILANG & KITA TAMBAHKAN KEMBALI
     */
    public function category()
    {
        // Pengajuan ini terhubung ke satu Category (Bagian) melalui kolom 'category_id'
        return $this->belongsTo(Category::class, 'category_id');
    }
}

