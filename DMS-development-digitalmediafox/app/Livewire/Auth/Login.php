<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use App\Traits\Auth\LoginValidationTrait;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    use LoginValidationTrait;

    public $email, $password, $remember = false;

    protected function getAuthService()
    {
        return app(AuthService::class);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login()
    {
        $validated = $this->validated();
        $validated['remember'] = $this->remember;

        if ($this->getAuthService()->login($validated)) {
           
             return $this->redirectRoute('dashboard', navigate: true);

        }

        $this->addError('email', translate('Invalid Email or Password'));
    }
}
