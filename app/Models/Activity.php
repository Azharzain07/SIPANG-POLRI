<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'nama_aktivitas',
    ];

    /**
     * Get the program that owns the activity.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the KROs for the activity.
     */
    public function kros()
    {
        return $this->hasMany(Kro::class);
    }
}
