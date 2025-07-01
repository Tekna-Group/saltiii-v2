<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
class UserController extends Controller
{
    //
      public function index()
    {
        $users = User::get();
        return view('users.index',
            array(
                'users' => $users,
            )
        );
    }

      public function store(Request $request)
    {
        // dd($request->all());
          $this->validate($request, [
            'name' => 'required|min:3|max:50',
            'email' => 'email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);


        $new_account = new User;
        $new_account->name = $request->name;
        $new_account->email = $request->email;
        $new_account->role = $request->role;
        $new_account->password = bcrypt($request->password);
        $new_account->status = "Active";
        $new_account->save();

        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }

    public function editUser(Request $request,$id)
    {
        // dd($request->all());
        $this->validate($request, [
            'email' => 'unique:users,email,' . $id,
        ]);

        $account = User::where('id', $id)->first();
        $account->name = $request->name;
        $account->email = $request->email;
        $account->role = $request->role;
        $account->save();


        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }
}
