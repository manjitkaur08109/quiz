<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
