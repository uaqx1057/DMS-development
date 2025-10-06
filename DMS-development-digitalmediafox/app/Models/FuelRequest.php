<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'total_orders',
        'files',
        'status'
    ];

    // Casting file_paths as an array
    protected $casts = [
        'files' => 'array',
    ];

}
