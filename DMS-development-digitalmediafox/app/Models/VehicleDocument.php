<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleDocument extends Model
{
     use HasFactory;
     protected $fillable = [
        'vehicle_id',
        'file_path',
    ];

   


}
