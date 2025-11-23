<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmountTransfer extends Model
{
    protected $fillable = [
        'supervisor_id',
        'created_by',
        'payment_type',
        'amount',
        'receipt_image',
        'receipt_date',
        'branch_id',
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
