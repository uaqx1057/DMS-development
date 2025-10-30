<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoordinatorReport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the report_fields for the CoordinatorReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function report_fields(): HasMany
    {
        return $this->hasMany(CoordinatorReportFieldValue::class);
    }

    /**
     * Get the driver that owns the CoordinatorReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The business that belong to the CoordinatorReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class);
    }
}
