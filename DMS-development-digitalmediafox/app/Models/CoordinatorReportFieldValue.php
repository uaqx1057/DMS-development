<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordinatorReportFieldValue extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the field that owns the CoordinatorReportFieldValue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
    
    public function driver()
        {
        return $this->belongsTo(Driver::class);
    }
    public function coordinatorReport(): BelongsTo
    {
        return $this->belongsTo(CoordinatorReport::class);
    }

}
