<?php

namespace App\Http\Controllers;

use App\Models\EmailModel;
use App\Models\User;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailModel::latest();
        if($request->search){
            $query->where('subject', 'like', '%' . $request->search . '%')
            ->orWhere('message', 'like', '%' . $request->search . '%');
        }

        $emaillogs = $query->paginate($request->per_page ?? 5);
      
        return $this->actionSuccess(
            "Success", $emaillogs
        );
    }
 public function delete($id)
    {
       $email = EmailModel::findOrFail($id);
       $email->delete();
       return $this->actionSuccess( 'Email deleted successfully' );
    }

}
