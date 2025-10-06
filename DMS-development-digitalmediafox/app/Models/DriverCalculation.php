<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCalculation extends Model
{
    use HasFactory;
    protected $fillable = ['business_id', 'calculation_type', 'from', 'to', 'amount'];

}
