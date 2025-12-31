<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\ContactMail;
use App\Models\EmailModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return $this->actionSuccess('success',
             $request->user(),
        );
    }
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|min:3',
            'phone_no' => 'nullable|integer',
        ]);

        $user->update([
            'name' => $request->name,
            'phone_no' => $request->phone_no,
        ]);

        return $this->actionSuccess(
            'Profile updated successfully!',
            $user
        );
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|min:6|confirmed', 
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);
    
         $subject = 'Your Password Changed Successfully';       
        $mailData ="
          Hello {$user->name},
          Email: {$user->email},
        Your account password has been changed successfully.
        If this was not you, please contact support immediately.
    ";
       
               SendEmailJob::dispatch($mailData , $subject , $user->email);

        return $this->actionSuccess(
            'Password changed successfully!'
        );
    }
}


