<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class DriverForgotPassword extends Component
{
    public $email;
    public $status;
    public $error;

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email|exists:drivers,email',
        ]);

        $status = Password::broker('drivers')->sendResetLink([
            'email' => $this->email,
        ]);

        if ($status == Password::RESET_LINK_SENT) {
            $this->status = __($status);
        } else {
            $this->error = __($status);
        }
    }
    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.driver.forgot-password');
    }
}
