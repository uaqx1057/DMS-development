<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'fields', 'is_freelancer'];

    public function scopeSearch($query, $value){
        $query->where("name", "like", "%{$value}%");
    }

}
