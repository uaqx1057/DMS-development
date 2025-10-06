<?php

namespace App\Traits\Auth;

trait LoginValidationTrait
{
    public function validated(){
        return $this->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'sometimes'
        ]);
    }
}
