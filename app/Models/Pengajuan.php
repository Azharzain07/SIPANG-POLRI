<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tanggal_pengajuan',
        'uraian',
        'kppn',
        'ppk_user_id',
        'npwp_user_id',
        'sumber_dana_id',
        'program_id',
        'activity_id',
        'kro_id',
        'category_id',
        'pengajuan_id',
        'coa_id',
        'jumlah_diajukan',
        // Kolom status dan tanggal proses sebaiknya tidak diisi di sini,
        // karena biasanya di-update oleh admin melalui aksi terpisah.
    ];

    /**
     * Atribut yang harus di-casting ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'npwp_processed_at' => 'datetime',
        'ppk_processed_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi Model (Relationships)
    |--------------------------------------------------------------------------
    |
    | Mendefinisikan bagaimana model ini terhubung dengan model lain.
    |
    */

    /**
     * Relasi ke PengajuanDetail (satu Pengajuan memiliki banyak Rincian).
     */
    public function details()
{
    return $this->hasMany(PengajuanDetail::class);
}

    /**
     * Relasi ke User (satu Pengajuan dibuat oleh satu User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relasi ke Program.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Relasi ke Activity.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Relasi ke KRO.
     */
    public function kro()
    {
        return $this->belongsTo(Kro::class);
    }

    /**
     * Relasi ke SumberDana.
     */
    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class);
    }

    /**
     * Relasi ke Category (Bagian).
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}