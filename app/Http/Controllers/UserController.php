<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return $this->actionSuccess(
            "Success",
            User::where('role_id','!=',getRoleId('admin'))->latest()->get()
        );
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->actionFailure('User not found', 404);
        }
        $user->delete();
        return $this->actionSuccess('User deleted');
    }

    public function profile(Request $request)
    {
        return $this->actionSuccess(
            "Success",
            $request->user()
        );
    }

    public function impersonate(Request $request)
    {
        $user = Auth::user();
            
        if (!$user|| $user->hasRole('admin'))  {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $targetUser = User::findOrFail($request->user_id);
        $tokenResult = $targetUser->createToken('api_token');
        $token = $tokenResult->plainTextToken;

        $tokenResult->accessToken->update([
            'expires_at' => Carbon::now()->addDay(),
        ]);

        return $this->actionSuccess('Login successful', [
            'user' => $targetUser,
            'token' => $token,
            'expires_at' => Carbon::now()->addDay()->toDateTimeString(),
        ]);
    }
    public function currentUser(Request $request)
    {
        return $this->actionSuccess("Success", $request->user());
    }


    public function stopImpersonation(Request $request)
    {
        $admin = Auth::user();

        if (!$admin || $admin->hasRole ( 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return $this->actionSuccess('Impersonation stopped', [
            'user' => $admin,
            'token' => $token,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();
        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $request->role_id

            ]);
            $user->assignRole(getRoleName($request->role_id));

            DB::commit();
            return $this->actionSuccess('User created successfully', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->actionFailure($e->getMessage());
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->actionSuccess('User show', $user);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);
        $user = User::findOrFail($id);
        $updateData = [
            'name' => $request->name,
            'role_id' => $request->role_id
        ];

        if ($request->password) {
            $updateData['password'] = bcrypt($request->password);
        }
        $user->update($updateData);


        $user->assignRole(getRoleName($request->role_id));

        return $this->actionSuccess('User updated successfully', $user);
    }
}
