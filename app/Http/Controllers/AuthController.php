<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\ContactMail;
use App\Models\EmailModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);


        $user->assignRole('user');

        $user->notify(new UserNotification(
            'welcome',
            'Welcome to our app',
            'user',
            $user->id
        ));

        $admin = User::role('admin')->first();
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
       
        SendEmailJob::dispatch($mailData , $subject , $user->email);

        $token = $user->createToken('api_token')->plainTextToken;
        return $this->actionSuccess('User registered successfully', [
            'user' => $user,
            'token' => $token,
        ]);
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->actionFailure('Invalid credentials');
        }

        $user = Auth::user();
        $tokenResult = $user->createToken('api_token');
        $token = $tokenResult->plainTextToken;

        $tokenResult->accessToken->update([
            'expires_at' => Carbon::now()->addDay(),
        ]);

        return $this->actionSuccess('Login successful', [
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'token' => $token,
            'expires_at' => Carbon::now()->addDay()->toDateTimeString(),
        ]);
    }
    function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->actionSuccess('Logout successful');
    }
}
