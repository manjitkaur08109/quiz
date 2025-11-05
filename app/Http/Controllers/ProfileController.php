<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
     // ✅ Get logged-in user profile
    public function show(Request $request)
    {
        return $this->actionSuccess(
             $request->user(),
        );
    }
     // ✅ Update profile details (name, email, phone)
    public function update(Request $request)
    {
        $user = $request->user();

        // Validation
        $request->validate([
            'name' => 'required|string|min:3',
            'phone_no' => 'nullable|integer',
        ]);

        // Update kar do
        $user->update([
            'name' => $request->name,
            'phone_no' => $request->phone_no,
        ]);

        return $this->actionSuccess(
            'Profile updated successfully!',
            $user
        );
    }

    // ✅ Change password
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|min:6|confirmed', // password_confirmation field aani chahiye
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->actionSuccess(
            'Password changed successfully!'
        );
    }
}


