<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAttendance extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'checkin_time', 'checkout_time', 'meter_reading', 'meter_image', 'car_image', 'out_meter_reading', 'out_meter_image'];
}
