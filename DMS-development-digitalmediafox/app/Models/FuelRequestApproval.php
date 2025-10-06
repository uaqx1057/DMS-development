<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelRequestApproval extends Model
{
    protected $fillable = [
    'fuel_id',
    'approved_by',
    'approved_amount',
    'approved_fuel_type',
    'estimated_cost',
    'scheduled_date',
    'notes',
];



public function approvedByUser()
{
    return $this->belongsTo(\App\Models\User::class, 'approved_by', 'id');
}


}
