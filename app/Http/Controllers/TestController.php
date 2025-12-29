<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
  public function testMail(){
   
    $user = User::where('email','ramesh@eligocs.com')->first();
 $admin = User::where('role_id',getRoleId('admin'))->first();
        if ($admin) {
            $admin->notify(new UserNotification(
                'new_user',
                'A new user has registered',
                'admin',
                $admin->id
            ));
        }

        $messageText = "
New User Registered Successfully

Name: {$user->name}
Email: {$user->email}
Role: User
Registered At: {$user->created_at->format('d-m-Y H:i')}
";

        $mailData = [
            'message' => $messageText
        ];
        $subject = 'New User Registered';

        Mail::to($user->email)->send(new ContactMail($subject,$mailData ));

        if ($admin) {
            Mail::to($admin->email)->send(new ContactMail($subject,$mailData));
        }
  
 return $this->actionSuccess('Email sent successfully');

  }

}
