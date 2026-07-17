<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OccupiedTent extends Model
{
    use HasFactory;

    protected $table = 'occupied_tents';

    protected $fillable = [
        'tent_code',
        'barangay_code',
    ];
}
