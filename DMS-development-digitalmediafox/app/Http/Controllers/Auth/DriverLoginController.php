<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.driver-login');
    }

   public function login(Request $request)
    {
        $request->validate([
            'iqaama_number' => 'required|string',
            'password' => 'required|string',
        ]);
    
        if (Auth::guard('driver')->attempt([
            'iqaama_number' => $request->iqaama_number,
            'password' => $request->password,
        ])) {
            
            $driver = Auth::guard('driver')->user();
            $driver->update(['status' => 'Active']);
    
            return redirect()->route('driver.dashboard');
        }
    
        return back()->withErrors([
            'iqaama_number' => 'Invalid Iqaama number or password.',
        ]);
    }

   public function logout(Request $request)
    {
        $driver = Auth::guard('driver')->user();
    
        if ($driver) {
    
            $driver->update(['status' => 'Inactive']);
        }
    
        Auth::guard('driver')->logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('driver.login');
    }

}
