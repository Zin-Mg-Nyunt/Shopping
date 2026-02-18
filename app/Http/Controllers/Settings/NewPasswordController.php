<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NewPasswordController extends Controller
{
    public function show(Request $request){
        $email = session('email');
        $keyword = session('keyword');
        if($email) session()->keep(['email', 'keyword']);
        if(!$keyword || $keyword !== 'success_otp_verify') return back();
        return inertia('auth/ResetPassword',[
            'email' => $email,
        ]);
    }
    public function reset(Request $request){
        $email = session('email');
        if($email) session()->keep(['email', 'keyword']);

       $request->validate([
        'password' => ['required', 'string', 'confirmed'],
       ]);
       
       $user = User::where('email', $email)->first();
       if($user){
        $user->password = $request->password;
        $user->save();
        session()->forget(['email', 'keyword']);
        return redirect()->route('login')->with('status', 'Password updated successfully');
       }

       return redirect()->back()->withErrors(['password' => 'Invalid password']);
       
    }
}
