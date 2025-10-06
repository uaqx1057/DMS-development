<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $guarded = ['id'];

    public function scopeSearch($query, $value){
        $query->where("name", "like", "%{$value}%")->orWhere("iqaama_number", "like", "%{$value}%");
    }

    /**
     * Get the driver_type that owns the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver_type(): BelongsTo
    {
        return $this->belongsTo(DriverType::class);
    }

    /**
     * Get all of the driver_attendance for the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function driver_attendance(): HasMany
    {
        return $this->hasMany(DriverAttendance::class);
    }

    /**
     * Get all of the orders for the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the devices for the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(DriverDevice::class);
    }

    /**
     * Get all of the fuel_requests for the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fuel_requests(): HasMany
    {
        return $this->hasMany(FuelRequest::class);
    }

    /**
     * The coordinator_report that belong to the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function coordinator_report(): HasMany
    {
        return $this->hasMany(CoordinatorReport::class);
    }

    /**
     * Get the branch that owns the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The businesses that belong to the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class);
    }
}
