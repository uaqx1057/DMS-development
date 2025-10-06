<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use App\Traits\Auth\LoginValidationTrait;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.auth')]
class Reset extends Component
{
    public $email;
    public $token;
    public $password;
    public $invalidToken = false;
   

    


   public function mount()
    {
        $this->email = request()->query('email');
        $this->token = request()->query('token');

        if (!$this->email || !$this->token) {
            $this->invalidToken = true;
            return;
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (
            !$record ||
            !password_verify($this->token, $record->token)
        ) {
            $this->invalidToken = true;
            session()->flash('error', 'Invalid or expired reset link.');
           
        }
    }
    
   


    public function render()
    {
        
        return view('livewire.auth.reset');
    }
    

public $password_confirmation;


public function resetPass()
{
    // Basic validation (you can customize rules)
    $this->validate([
        'password' => 'required|min:6|confirmed',
    ]);

    // Get record from DB
    $record = DB::table('password_reset_tokens')
        ->where('email', $this->email)
        ->first();

    if (!$record || !password_verify($this->token, $record->token)) {
        session()->flash('error', 'Invalid or expired reset token.');
        return;
    }

    // Get user by email
    $user = \App\Models\User::where('email', $this->email)->first();

    if (!$user) {
        session()->flash('error', 'User not found.');
        return;
    }

    // Update password
    $user->update([
        'password' => bcrypt($this->password)
    ]);

    // Delete the reset token
    DB::table('password_reset_tokens')->where('email', $this->email)->delete();
    
    $this->invalidToken = true;
    session()->flash('status', 'Password has been reset successfully.');

   
}

    

   
}
