<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'nama_coa',
        'pagu',
        'sisa_pagu',
    ];

    /**
     * Get the account that owns the coa.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}