<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;

class AdminForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('livewire.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $email = $request->input('email');
    
       
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return back()->withInput()->with('error', 'User not found with this email.');
        }
    
        $token = Str::random(64);
    
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => bcrypt($token),
                'created_at' => Carbon::now(),
            ]
        );
    
        $resetLink = url('/reset-password?token=' . $token . '&email=' . urlencode($email));
    
        Mail::raw("Click this link to reset your password:\n\n$resetLink", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Link');
        });
    
       return back()->withInput()->with('status', 'Password reset email sent successfully.');

    }

}
