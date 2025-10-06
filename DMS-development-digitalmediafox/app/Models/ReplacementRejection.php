<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplacementRejection extends Model
{
    protected $fillable = [
    'replacement_id',
    'rejection_note',
    'rejected_by',
];

}
