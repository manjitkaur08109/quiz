<?php

namespace App\Http\Controllers;

use App\Models\EmailModel;
use App\Models\User;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $email = EmailModel::latest()->get();
        return $this->actionSuccess(
            "Success", $email
        );
    }
 public function delete($id)
    {
       $email = EmailModel::findOrFail($id);
       $email->delete();
       return $this->actionSuccess( 'Email deleted successfully' );
    }

}
