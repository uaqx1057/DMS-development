<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modesl\VehicleReplacement;
use App\Modesl\Vehicle;

class VehicleReplacementApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'replacement_id',
        'replacement_vehicle',
        'replacement_period',
        'expected_return_date',
        'approval_notes',
        'approved_by',
    ];

    // Relationships
    public function replacement()
    {
        return $this->belongsTo(VehicleReplacement::class, 'replacement_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'replacement_vehicle');
    }

    public function approval()
    {
        return $this->hasOne(VehicleReplacementApproval::class, 'replacement_id');
    }
}
