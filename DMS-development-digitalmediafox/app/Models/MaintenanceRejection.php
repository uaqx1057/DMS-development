<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRejection extends Model
{
    protected $fillable = [
    'maintenance_id',
    'rejection_note',
    'rejected_by',
];

}
