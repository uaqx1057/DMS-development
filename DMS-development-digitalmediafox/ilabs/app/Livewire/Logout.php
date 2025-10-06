<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function render()
    {
        return view('livewire.logout');
    }

    public function logout(){
        Auth::logout();
        session()->regenerate();
        return $this->redirectRoute('login', navigate:true);
    }
}
