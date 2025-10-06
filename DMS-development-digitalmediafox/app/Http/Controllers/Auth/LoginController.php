<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Ensure this is the correct User model

class LoginController extends Controller
{
    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Your email format is invalid.',
            'password.required' => 'Please enter your password.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->status === 'Inactive') {
                return back()->withErrors([
                    'email' => 'Your account is inactive.',
                ])->withInput();
            }

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                return redirect('/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput();
    }
}
