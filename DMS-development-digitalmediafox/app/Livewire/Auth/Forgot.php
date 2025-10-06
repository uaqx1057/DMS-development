<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use App\Traits\Auth\LoginValidationTrait;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.auth')]
class Forgot extends Component
{
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    
}
