<?php

namespace App\Repositories;

use App\Interfaces\AuthInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthInterface
{
    public function login(array $data)
    {
        $remember = $data['remember'] ?? false;
        unset($data['remember']);

        if(Auth::attempt($data, $remember)){
            session()->regenerate();
            session()->flash('success', translate('Logged in successfully!'));
            return true;
        }

        return false;
    }
}
