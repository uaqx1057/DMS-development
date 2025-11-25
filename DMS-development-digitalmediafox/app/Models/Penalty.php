<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'business_id',
        'business_id_value',
        'coordinator_report_id',
        'penalty_date',
        'penalty_value',
        'penalty_file',
        'is_from_coordinate'
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
    public function business() { return $this->belongsTo(Business::class); }
    public function businessValue() { return $this->belongsTo(BusinessId::class, 'business_id_value'); }
    public function coordinatorReport() { return $this->belongsTo(CoordinatorReport::class); }
}
