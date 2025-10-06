<?php

namespace App\Livewire\Employee;

use App\Traits\Employee\EmployeeTrait;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
#[Title('Create Employee')]
class CreateEmployee extends Component
{

    use WithFileUploads, EmployeeTrait;

    public string $main_menu = 'Employees';
    public string $menu = 'Create Employee';

    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $employees =  $this->employeeService->all();

        return view('livewire.employee.create-employee', compact('main_menu', 'menu', 'employees'));
    }

    public function create(){
            // * Applying Validations
            $validated = $this->validations();

            // * Storing Image
            if($this->image){
                $validated['image'] = $this->image->store('employees', 'public');
            }

            $validated['password'] = Hash::make($validated['password']);

            // * Creating Employee
            $this->employeeService->create($validated);

            session()->flash('success', translate('Employee Created Successfully!'));
            return $this->redirectRoute('employee.index', navigate:true);
    }




}
