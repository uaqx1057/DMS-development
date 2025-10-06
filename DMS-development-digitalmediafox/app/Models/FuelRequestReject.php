<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelRequestReject extends Model
{
    protected $fillable = [
    'fuel_id',
    'rejected_by',
    'notes',
];


public function rejectedByUser()
{
    return $this->belongsTo(User::class, 'rejected_by');
}

}
