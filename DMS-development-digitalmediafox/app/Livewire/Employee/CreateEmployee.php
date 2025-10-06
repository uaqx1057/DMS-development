<?php

namespace App\Livewire\Employee;

use Illuminate\Validation\ValidationException;
use App\Services\DesignationService;
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
    public $form = [];
    


    public function render()
    {
        $main_menu = $this->main_menu;
        $menu = $this->menu;
        $employees =  $this->employeeService->all();
        
        $designations = cache()->rememberForever('designations', function () {
            return $this->designationService->all();
        });
        
        $departments = cache()->rememberForever('departments', function () {
            return $this->departmentService->all();
        });

         $genders = getGenders();

        return view('livewire.employee.create-employee', compact('main_menu', 'menu', 'employees','designations','departments','genders'));
    }

   public function generateUniqueUserId(): string
    {
        // Get the last user_id from the users table
        $lastUser = \App\Models\User::where('user_id', 'LIKE', 'E%')
            ->orderByRaw("CAST(SUBSTRING(user_id, 2) AS UNSIGNED) DESC")
            ->first();
    
        // Extract numeric part and increment it
        $lastNumber = $lastUser ? (int) substr($lastUser->user_id, 1) : 1000;
    
        $newNumber = $lastNumber + 1;
    
        // Return the new ID with prefix
        return 'E' . $newNumber;
    }












    public function create(){
            // * Applying Validations
            $validated = $this->validations();
            // * Generate Unique User ID
            
            $this->user_id = $this->generateUniqueUserId();
            $validated['user_id'] = $this->user_id; 
        


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
