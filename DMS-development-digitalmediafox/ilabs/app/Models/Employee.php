<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes;

    public function scopeSearch($query, $value){
        $query->where("name", "like", "%{$value}%")->orWhere("iqaama_number", "like", "%{$value}%");
    }

    protected $fillable = ['designation_id','department_id','role_id','employment_type_id','branch_id','employee_id','salutation','name',
'email','image','password','country','mobile','gender','joining_date','reporting_to','languages','address','about',
'is_login_allowed','is_receive_email_notification','hourly_rate','slack_memember_id','skills','probation_end_date','notice_period_start_date','notice_period_end_date','marital_status'];
}
