<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function create()
    {
        $email = session('email');
        $expiredAt = session('expiredAt');
        $status = session('status');
        if($email) session()->keep(['email', 'expiredAt', 'status']);
        
        return inertia('auth/ForgotPassword',[
            'email' => $email,
            'expiredAt' => $expiredAt,
            'status' => $status,
        ]);
    }

    public function store(Request $request)
    {
        $validated=$request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        DB::transaction(function() use ($validated){
            $randomCode = rand(100000, 999999);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']], 
                [
                    'token' => Hash::make($randomCode), 
                    'created_at' => now()
                ]
            );
            Mail::to($validated['email'])->queue(new OtpCodeMail($randomCode));
        });
        $expiredAt = now()->addMinutes(3)->timestamp;
        return back()->with([
            'email' => $validated['email'],
            'expiredAt' => $expiredAt,
            'status' => 'OTP code sent to your email',
        ]);
    }

    public function changeEmail(){
        session()->forget(['email', 'expiredAt', 'status']);
        return back();
    }
}
