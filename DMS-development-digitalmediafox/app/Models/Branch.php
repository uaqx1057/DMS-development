<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    /**
     * Get all of the drivers for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function coordinatorReports(): HasMany
    {
        return $this->hasMany(CoordinatorReport::class);
    }
    public function amountTransfer(): HasMany
    {
        return $this->hasMany(AmountTransfer::class);
    }

    public function walletRecharges(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            CoordinatorReportFieldValue::class,  // Final model
            CoordinatorReport::class,            // Through model
            'branch_id',                         // Foreign key on CoordinatorReport (branch_id)
            'coordinator_report_id',             // Foreign key on CoordinatorReportFieldValue
            'id',                                // Local key on Branch
            'id'                                 // Local key on CoordinatorReport
        )->where('field_id', 22);               // Only wallet recharge
    }

    public function cashCollected(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            CoordinatorReportFieldValue::class,
            CoordinatorReport::class,
            'branch_id',
            'coordinator_report_id',
            'id',
            'id'
        )->where('field_id', 7);
    }
}
