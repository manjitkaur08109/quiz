<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller {
    public function index( Request $request ) {

        return $this->actionSuccess(
            "Success",
            User::where('account_type' , 'user')->latest()->get()
        );
    }

    public function destroy( $id ) {
        $user = User::find( $id );
        if ( !$user ) {
            return $this->actionFailure( 'User not found', 404 );
        }
        $user->delete();
        return $this->actionSuccess( 'User deleted' );
    }

    public function profile( Request $request ) {
        return $this->actionSuccess(
            "Success",
            $request->user()
        );
    }

    public function impersonate(Request $request)
{
    $user = Auth::user();

    if ($user->account_type !== 'admin') {
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

        if (!$admin || $admin->account_type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return $this->actionSuccess('Impersonation stopped', [
            'user' => $admin,
            'token' => $token,
        ]);
    }
}
