<?php

namespace App\Traits\Employee;
use Illuminate\Support\Str;

use App\Services\{
    EmployeeService,
};
use Illuminate\Validation\Rule;

trait EmployeeTrait
{
    // Services
    protected EmployeeService $employeeService;

    protected $employee;

    // Properties
    public ?string $employeeId = null;
    public ?string $salutation = 'Mr';
    public ?string $name = null;
    public ?string $email = null;
    public ?string $mobile = null;
    public ?string $dob = null;
    public ?string $gender = 'Male'; // Default to 'Male'
    public ?string $marital_status = 'Single'; // or 'Married', etc.
    public ?string $status = 'Active';
    public ?string $password = null;
    public $image;
    public ?string $designation_id = '';
    public ?string $department_id = '';
    public ?string $branch_id = '1';
    public ?string $country_id = '1';
    public ?string $language_id = '1';
    public ?string $joining_date = null;
    public ?string $reporting_to = null;
    public ?string $role_id = '1';
    public ?string $address = null;
    public ?string $about = null;
    public ?string $is_login_allowed = '1';
    public ?string $is_receive_email_notification = '1';
    public ?int $hourly_rate = 0;
    public ?string $slack_member_id = null;
    public ?string $skills = '';
    public ?string $probation_end_date = null;
    public ?string $notice_period_start_date = null;
    public ?string $notice_period_end_date = null;
    public ?string $employment_type_id = '1';
    public ?string $user_id = null;



public function mount($id = null)
{
    if ($id) {
        $this->employee = $this->employeeService->find($id);
        $this->employeeId = $id;

        $this->salutation = (string) $this->employee->salutation;
        $this->name = $this->employee->name;
        $this->email = $this->employee->email;
        $this->mobile = $this->employee->mobile;
        $this->dob = $this->employee->dob;
        $this->gender = (string) $this->employee->gender;
        $this->marital_status = (string) $this->employee->marital_status;
        $this->status = (string) $this->employee->status;

        $this->designation_id = (string) $this->employee->designation_id;
        $this->department_id = (string) $this->employee->department_id;
        $this->branch_id = $this->employee->branch_id ? (string) $this->employee->branch_id : null;
        $this->country_id = $this->employee->country_id ? (string) $this->employee->country_id : null;
        $this->language_id = $this->employee->language_id ? (string) $this->employee->language_id : null;
        $this->joining_date = $this->employee->joining_date;
        $this->reporting_to = $this->employee->reporting_to ? (string) $this->employee->reporting_to : null;
        $this->role_id = $this->employee->role_id ? (string) $this->employee->role_id : null;

        $this->address = $this->employee->address;
        $this->about = $this->employee->about;
        $this->is_login_allowed = (string) $this->employee->is_login_allowed;
        $this->is_receive_email_notification = (string) $this->employee->is_receive_email_notification;
        $this->hourly_rate = $this->employee->hourly_rate;
        $this->slack_member_id = $this->employee->slack_member_id;
        $this->skills = $this->employee->skills;
        $this->probation_end_date = $this->employee->probation_end_date;
        $this->notice_period_start_date = $this->employee->notice_period_start_date;
        $this->notice_period_end_date = $this->employee->notice_period_end_date;
        $this->employment_type_id = $this->employee->employment_type_id ? (string) $this->employee->employment_type_id : null;
    }
}

    public function boot(
        EmployeeService $employeeService,
    ) {
        $this->employeeService = $employeeService;
    }

    public function validations()
    {
        

        $validations = [
            'salutation' => 'string',
            'name' => 'required|string|min:3',
            'mobile' => 'sometimes',
            'dob' => 'sometimes|date',
            'gender' => 'sometimes',
            'marital_status' => 'sometimes',
            'status' => 'sometimes',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'designation_id' => ['required'],
            'department_id' => 'required',
            'branch_id' => 'sometimes',
            'country_id' => 'sometimes',
            'language_id' => 'sometimes',
            'joining_date' => 'sometimes|date',
            'reporting_to' => 'required',
            'role_id' => 'sometimes',
            'is_login_allowed' => 'required|in:,1,0',
            'is_receive_email_notification' => 'required|in:,1,0',
            'hourly_rate' => 'sometimes|numeric',
            'slack_member_id' => 'sometimes|max:255',
            'skills' => 'sometimes|string',
            'probation_end_date' => 'sometimes|date',
            'notice_period_start_date' => 'sometimes|date',
            'notice_period_end_date' => 'sometimes|date',
            'employment_type_id' => 'sometimes',
        ];

        if($this->employeeId != null){
            $validations['email'] = ['required', 'email', Rule::unique('users')->whereNull('deleted_at')->ignore($this->employeeId)];
        }else{
            $validations['email'] = ['required', 'email', Rule::unique('users')->whereNull('deleted_at')];
            $validations['password'] ='required|string|min:6';
        }

      
        return $this->validate($validations);
        
        
    }

    public function generatePassword(){
        $this->password = Str::random(8);
    }
}
