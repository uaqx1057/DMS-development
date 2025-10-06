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
    public ?string $salutation = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $mobile = null;
    public ?string $dob = null;
    public ?string $gender = null;
    public ?string $marital_status = null;
    public ?string $status = null;
    public ?string $password = null;
    public $image;
    public ?string $designation_id = null;
    public ?string $department_id = null;
    public ?string $branch_id = null;
    public ?string $country_id = null;
    public ?string $language_id = null;
    public ?string $joining_date = null;
    public ?string $reporting_to = null;
    public ?string $role_id = null;
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
    public ?string $employment_type_id = null;


    public function mount($id = null){
        if($id)
        {
            $this->employee = $this->employeeService->find($id);
            $this->employeeId = $id;
            $this->salutation = $this->employee->salutation;
            $this->name = $this->employee->name;
            $this->email = $this->employee->email;
            $this->mobile = $this->employee->mobile;
            $this->dob = $this->employee->dob;
            $this->gender = $this->employee->gender;
            $this->marital_status = $this->employee->marital_status;
            $this->status = $this->employee->status;
            $this->designation_id = $this->employee->designation_id;
            $this->department_id = $this->employee->department_id;
            $this->branch_id = $this->employee->branch_id;
            $this->country_id = $this->employee->country_id;
            $this->language_id = $this->employee->language_id;
            $this->joining_date = $this->employee->joining_date;
            $this->reporting_to = $this->employee->reporting_to;
            $this->role_id = $this->employee->role_id;
            $this->address = $this->employee->address;
            $this->about = $this->employee->about;
            $this->is_login_allowed = $this->employee->is_login_allowed;
            $this->is_receive_email_notification = $this->employee->is_receive_email_notification;
            $this->hourly_rate = $this->employee->hourly_rate;
            $this->slack_member_id = $this->employee->slack_member_id;
            $this->skills = $this->employee->skills;
            $this->probation_end_date = $this->employee->probation_end_date;
            $this->notice_period_start_date = $this->employee->notice_period_start_date;
            $this->notice_period_end_date = $this->employee->notice_period_end_date;
            $this->employment_type_id = $this->employee->employment_type_id;
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
            'salutation' => 'sometimes|string',
            'name' => 'required|string|min:3',
            'mobile' => 'sometimes',
            'dob' => 'sometimes|date',
            'gender' => 'sometimes|string',
            'marital_status' => 'sometimes|string',
            'status' => 'sometimes|string',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'designation_id' => 'required|exists:designations,id',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'sometimes|exists:branches,id',
            'country_id' => 'sometimes|exists:countries,id',
            'language_id' => 'sometimes|exists:languages,id',
            'joining_date' => 'sometimes|date',
            'reporting_to' => 'sometimes|exists:users,id',
            'role_id' => 'sometimes|exists:roles,id',
            'is_login_allowed' => 'required|in:,1,0',
            'is_receive_email_notification' => 'required|in:,1,0',
            'hourly_rate' => 'sometimes|numeric',
            'slack_member_id' => 'sometimes|max:255',
            'skills' => 'sometimes|string',
            'probation_end_date' => 'sometimes|date',
            'notice_period_start_date' => 'sometimes|date',
            'notice_period_end_date' => 'sometimes|date',
            'employment_type_id' => 'sometimes|exists:employment_types,id',
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
