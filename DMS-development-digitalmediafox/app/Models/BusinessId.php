<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessId extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the business that owns the BusinessId
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_business_ids')
                    ->withPivot(['previous_driver_id', 'assigned_at', 'transferred_at'])
                    ->withTimestamps();
    }

    public function currentDriver()
    {
        return $this->drivers()->wherePivot('transferred_at', null)->first();
    }

    public function assignmentHistory()
    {
        return $this->hasMany(DriverBusinessId::class)->orderBy('assigned_at', 'desc');
    }
}