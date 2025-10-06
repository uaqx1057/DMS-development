<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{


    protected $fillable = [
        'vehicle_id',
        'status',
        'fuel_type',
        'request_amount',
        'reason_for_request',
        'number_of_order_deliver',
        'upload_order_screenshort',
        'additional_notes',
        'requested_by',
    ];
    
    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    
    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'requested_by');
    }

    public function approval()
    {
        return $this->hasOne(\App\Models\FuelRequestApproval::class, 'fuel_id', 'id');
    }
    
    public function rejection()
    {
        return $this->hasOne(\App\Models\FuelRequestReject::class, 'fuel_id');
    }
}
