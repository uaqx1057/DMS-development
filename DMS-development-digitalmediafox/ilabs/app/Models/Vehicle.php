<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'registration_number',
        'make',
        'model',
        'year',
        'vin',
        'current_location',
        'status',
        'fuel_type',
        'mileage',
        'notes',
    ];
}
