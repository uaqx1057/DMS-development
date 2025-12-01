<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRecharge extends Model
{
    protected $fillable = [
        'driver_id',
        'user_id',
        'mobile',
        'opearator',
        'status',
        'reason',
        'approved_by',
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function recharge()
    {
        return $this->hasOne(Recharge::class);
    }

    public function approved()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
