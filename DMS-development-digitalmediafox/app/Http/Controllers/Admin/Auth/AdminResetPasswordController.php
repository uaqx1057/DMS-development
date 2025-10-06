<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminResetPasswordController extends Controller
{
  // AdminResetPasswordController.php

public function showResetForm(Request $request)
{
    return view('livewire.auth.reset-password', [
        'token' => $request->token,
        'email' => $request->email
    ]);
}

public function reset(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $reset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$reset || !password_verify($request->token, $reset->token)) {
        return back()->with('error', 'Invalid or expired reset token.');
    }

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    $user->update([
        'password' => bcrypt($request->password)
    ]);

    // Delete token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('status', 'Password has been reset.');
}

}

?>