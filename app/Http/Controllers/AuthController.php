<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        'email' =>$request->email,
        'password' =>bcrypt($request->password),
    ]);

     $token = $user->createToken('api_token')->plainTextToken;
        return $this->actionSuccess('User registered successfully', [
            'user' => $user,
            'token' => $token,
        ]);
    return $this->actionSuccess('User registered successfully',$token);
}
  // 🔹 Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->actionSuccess('Invalid credentials');
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return $this->actionSuccess('Login successful',
           [ 'user' => $user,
            'token' => $token,]
        );
    }

}
