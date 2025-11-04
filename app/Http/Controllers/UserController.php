<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function index( Request $request ) {
        $user = $request->user();

        if ( !$user ) {
            return $this->actionFailure( 'Unauthorized', 401 );
        }
        return $this->actionSuccess(
            200,
            \App\Models\User::latest()->get()
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
            200,
            $request->user()
        );
    }
}
