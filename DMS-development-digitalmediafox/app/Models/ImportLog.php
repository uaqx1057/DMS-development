<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'original_name',
        'report_date',
        'model',
        'rows_imported',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
