<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDifference extends Model
{
    protected $fillable = [
        'driver_id',
        'user_id',
        'total_receipt',
        'total_paid',
        'total_remaining',
        'receipt_date',
        'receipt_image',
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
    public function user() { return $this->belongsTo(User::class); }


}
