<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return $this->actionSuccess(
            "Success",
            User::where('role_id', '!=', getRoleId('admin'))->latest()->get()
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
        $admin = Auth::user();

        // ✅ Only admins can impersonate
        if (!$admin || !$admin->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $targetUser = User::findOrFail($request->user_id);

        // ❌ Prevent admin → admin impersonation
        if ($targetUser->hasRole('admin')) {
            return response()->json(['message' => 'Cannot impersonate admin'], 403);
        }

        // ✅ Create impersonation token
        $tokenResult = $targetUser->createToken('impersonation_token');
        $token = $tokenResult->plainTextToken;

        $tokenResult->accessToken->update([
            'expires_at' => now()->addHours(12),
        ]);


        return $this->actionSuccess('Impersonation started', [
            'user' => $targetUser,
            'token' => $token,
            'permissions' => $targetUser->getAllPermissions()->pluck('name'),
            'expires_at' => now()->addHours(12)->toDateTimeString(),
        ]);
    }

    public function currentUser(Request $request)
    {
        return $this->actionSuccess("Success", $request->user());
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
