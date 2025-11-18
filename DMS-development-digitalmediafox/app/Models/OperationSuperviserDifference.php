<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationSuperviserDifference extends Model
{
    protected $fillable = [
        'superviser_id',
        'created_by',
        'total_receipt',
        'total_paid',
        'total_remaining',
        'receipt_image',
    ];

    public function superviser()
    {
        return $this->belongsTo(User::class, 'superviser_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
