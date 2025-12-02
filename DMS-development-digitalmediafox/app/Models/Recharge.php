<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $casts = [
        'date' => 'datetime',
    ];
    protected $fillable = [
        'request_recharge_id',
        'user_id',
        'amount',
        'date',
        'image',
    ];

    public function requestRecharge() { return $this->belongsTo(RequestRecharge::class); }
    public function user() { return $this->belongsTo(User::class); }
}
