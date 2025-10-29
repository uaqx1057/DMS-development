<?php

namespace App\Livewire\Driver\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $iqaama_number ;
    public $password;

   public function login()
    {
        
         $this->validate([
            'iqaama_number' => 'required',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('driver')->attempt([
            'iqaama_number' => $this->iqaama_number,
            'password' => $this->password,
        ])) {
            return redirect()->intended('/driver/dashboard');
        }

        $this->addError('iqaama_number', 'Invalid iqaama number or password.');
    
    }


  public function render()
    {
       return view('livewire.driver.auth.login')
    ->layout('components.layouts.plain');


    }

}
