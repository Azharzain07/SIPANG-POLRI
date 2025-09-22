<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengajuan_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pengajuan_id',
        'coa_id',
        'jumlah_diajukan',
    ];

    /**
     * Get the pengajuan that owns the detail.
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    /**
     * Get the COA associated with the detail.
     */
    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}