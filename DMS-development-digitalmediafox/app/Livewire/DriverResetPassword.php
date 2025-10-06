<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

class DriverResetPassword extends Component
{
    public $token;
    public $email;
    public $password;
    public $password_confirmation;
    public $status;
    public $error;

    public function mount($token)
    {
        $this->token = $token;
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email|exists:drivers,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::broker('drivers')->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($driver, $password) {
                $driver->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($driver));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            $this->status = __($status);
        } else {
            $this->error = __($status);
        }
    }

    public function render()
    {
        return view('livewire.driver-reset-password');
    }
}
