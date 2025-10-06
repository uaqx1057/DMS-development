<?php

namespace App\Repositories;

use App\Interfaces\AuthInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    public function login(array $data)
    {
        $remember = $data['remember'] ?? false;
        unset($data['remember']);
    
        $user = \App\Models\User::where('email', $data['email'] ?? null)->first();
    
        if (!$user) {
            session()->flash('error', 'Invalid Email or Password.');
            return false;
        }
    
        if (!Hash::check($data['password'], $user->password)) {
            session()->flash('error', 'Invalid Email or Password.');
            return false;
        }
    
        if ($user->status !== 'Active') {
            session()->flash('error', 'Your account is inactive.');
            return false;
        }
    
        Auth::login($user, $remember);
        session()->regenerate();
        session()->flash('success', 'Logged in successfully!');
        return true;
    }
}




