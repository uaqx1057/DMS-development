<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name', 'image'];


    /**
     * The fields that belong to the Business
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class);
    }

    /**
     * Get all of the fields for the Business
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function driver_calculations()
    {
        return $this->hasMany(DriverCalculation::class);
    }

    /**
     * The coordinator_report that belong to the Business
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function coordinator_report(): BelongsToMany
    {
        return $this->belongsToMany(CoordinatorReport::class);
    }

    /**
     * Get all of the orders for the Business
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * The drivers that belong to the Business
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class);
    }
}
