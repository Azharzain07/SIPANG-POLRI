<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_akun_belanja',
    ];

    /**
     * Get the COAs for the account.
     */
    public function coas()
    {
        return $this->hasMany(Coa::class);
    }
}
