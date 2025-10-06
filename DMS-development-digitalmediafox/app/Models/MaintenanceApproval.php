<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceApproval extends Model
{
    protected $fillable = [
    'maintenance_id',
    'estimated_cost',
    'scheduled_date',
    'approval_notes',
    'approved_by',
];

}
