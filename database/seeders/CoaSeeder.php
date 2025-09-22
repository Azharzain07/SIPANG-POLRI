<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Coa;

class Coa extends Model // <-- PASTIKAN NAMA CLASS INI BENAR
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'nama_coa',
        'pagu',
        'sisa_pagu',
    ];

    /**
     * Get the account that owns the COA.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}