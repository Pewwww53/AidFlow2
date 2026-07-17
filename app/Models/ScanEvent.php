<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'tent_code',
        'scanned_at',
        'barangay_code',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];
}
