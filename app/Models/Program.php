<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_program',
    ];

    /**
     * Get the activities for the program.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}