<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCoordinatorReport extends Model
{
    use HasFactory;

    protected $table = 'business_coordinator_report'; // custom table name

    protected $fillable = [
        'business_id',
        'coordinator_report_id',
    ];

    public $timestamps = true; // keep if table has created_at, updated_at
}
