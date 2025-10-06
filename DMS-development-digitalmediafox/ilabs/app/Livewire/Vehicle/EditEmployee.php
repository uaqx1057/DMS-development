<?php

namespace App\Livewire\Employee;

use App\Traits\Employee\EmployeeTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Edit Employee')]
class EditEmployee extends Component
{

    use WithFileUploads, EmployeeTrait;

    public string $main_menu = 'Employees';
    public string $menu = 'Edit Employee';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $employees =  $this->employeeService->all();
        $employee = $this->employee;
        return view('livewire.employee.edit-employee', compact('main_menu', 'menu', 'employees', 'employee'));
    }

    public function update(){
            // * Applying Validations
            $validated = $this->validations();

            // * Storing Image
            if($this->image){
                $validated['image'] = $this->image->store('employees', 'public');
            }

            if($this->password != ""){

                $validated['password'] = Hash::make($this->password);
            }

            // * Creating Employee
            $this->employeeService->update($validated, $this->employeeId);

            session()->flash('success', translate('Employee Updated Successfully!'));
            return $this->redirectRoute('employee.index', navigate:true);
    }




}
