<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kro extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'nama_kro',
        'lokasi',
    ];

    /**
     * Get the activity that owns the KRO.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
