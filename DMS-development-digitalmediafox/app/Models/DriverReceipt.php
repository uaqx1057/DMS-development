<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'booklet_id',
        'reaceipt_no',
        'receipt_date',
        'receipt_image',
        'driver_id',
        'business_id',
        'business_id_value',
        'amount_received',
        'user_id',
    ];

    public function booklet() { return $this->belongsTo(Booklet::class); }
    public function driver() { return $this->belongsTo(Driver::class); }
    public function business() { return $this->belongsTo(Business::class); }
    public function businessValue() { return $this->belongsTo(BusinessId::class, 'business_id_value'); }

    public function user() { return $this->belongsTo(User::class); }
}
