<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDevice extends Model
{
    protected $fillable = ['driver_id', 'fcm_token', 'device_id'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
