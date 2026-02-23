<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Pest\Support\Str;

class OtpVerifyController extends Controller
{
    public function verifyOtp(Request $request)
    {
        $email = session('email');
        $expiredAt = session('expiredAt');
        $status = session('status');
        if($email) session()->keep(['email', 'expiredAt', 'status']);

        $validated=$request->validate([
            'otp' => ['required', 'integer'],
        ]);

        $record=DB::table('password_reset_tokens')->where('email', $email)->first();
        $checkOtp = Hash::check($validated['otp'], $record?->token);
        $expired = $expiredAt <= now()->timestamp;
        if(!$record || !$checkOtp || $expired) {
            return back()
                    ->with([
                        'email' => $email,
                        'expiredAt' => $expiredAt,
                        'status' => $status,
                    ])
                    ->withErrors(['otp' => 'Invalid OTP']);
        };
        $token = Str::random(60);
        $keyword = 'success_otp_verify';
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        return redirect()->route('password.reset', ['token' => $token])->with(['email' => $email, 'keyword' => $keyword]);
    }   
}
