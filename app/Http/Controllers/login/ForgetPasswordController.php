<?php

namespace App\Http\Controllers\Login;

use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Illuminate\Support\Facades\Mail;
use Sentinel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ForgetPasswordController extends Controller
{
    public function forgotPasword(){
        return view('auth.forgotpassword');
    }

    /*
    |--------------------------------------------------------------------------
    | Post Forgot Password
    |--------------------------------------------------------------------------
    */

    public function postForgotPassword(Request $request){
        $user = User::whereEmail($request->email)->first();

        if(count($user) == 0)
            return redirect()->back()->with(['success' => 'Reset Code has been sent.']);

        $reminder = Reminder::exists($user) ?: Reminder::create($user);

        $this->sendEmail($user, $reminder->code);

        return redirect()->back()->with(['success' => 'Reset Code has been sent.']);

    }


    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function resetPassword($email, $reset_code){

        $user = User::whereEmail($email)->first();

        if(count($user) == 0){
            abort(404);
        }

        if($reminder = Reminder::exists($user)){
            if($reset_code == $reminder->code){
                return view('auth.resetPassword', compact('user','reminder'));
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Post Reset Password
    |--------------------------------------------------------------------------
    */

    public function postResetPassword(Request $request, $email, $reset_code){

        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        $user = User::whereEmail($email)->first();

        if(count($user) == 0){
            abort(404);
        }

        if($reminder = Reminder::exists($user)){
            if($reset_code == $reminder->code){
                Reminder::complete($user, $reset_code, $request->password);

                return redirect('/login')->with(['success' => 'Login with your New Password']);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }



    /*
    |--------------------------------------------------------------------------
    | Sending Email to User
    |--------------------------------------------------------------------------
    */

    public function sendEmail($user, $code){
        $mail_data = [
            'user' => $user,
            'code' => $code,
        ];

        Mail::send('email.forgotPassword', $mail_data, function ($message) use ($user){
            $message->to($user->email)->subject("$user->name reset your passowrd");
        });
    }
}
