<?php

namespace App\Http\Controllers;
use App\User;
use App\TaskActivity;
use App\Task;
use App\Project;
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
     public function avatar(Request $request,$id)
    {
        // dd($request->all());
        $user = User::findOrFail($id);
        if ($request->hasFile('file')) {
        }
       $attachment = $request->file('file');
        $original_name = $attachment->getClientOriginalName();
        $name = time().'_'.$attachment->getClientOriginalName();
        $attachment->move(public_path().'/avatars/', $name);
        $file_name = '/avatars/'.$name;
        $user->avatar = $file_name;
        $user->save();
        
        Alert::success('Successfully uploaded')->persistent('Dismiss');
        return back();
    }
    public function updatePassword(Request $request)
    {
        $validator = $request->validate([
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
    
        $user = User::findOrFail(auth()->user()->id);
        $user->password = bcrypt($request->input('password'));
        $user->save();

        Alert::success('Password Updated')->persistent('Dismiss');
        return redirect('/users');
    }

    public function view(Request $Request,$id)
    {
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
        
        $activities = TaskActivity::where('user_id',$id)->whereBetween('date', [$last_sunday, $saturday])->get();
        $user = User::findOrfail($id);
        $tasks = Task::with(['users', 'project', 'comments', 'attachments'])->whereHas('users', function ($query)  use ($id) {
            $query->where('user_id', $id);
        })->orderBy('due_date','asc')->get();
        $projects = Project::whereHas('users', function ($query) use ($id) {
            $query->where('user_id', $id);
        })->get();
        return view('users.view-profile',
            array(
                'user' => $user,
                'activities' => $activities,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                'tasks' => $tasks,
                'projects' => $projects,
                
            )
        );
    }
    public function viewProfile(Request $Request)
    {
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
        
        $activities = TaskActivity::where('user_id',auth()->user()->id)->whereBetween('date', [$last_sunday, $saturday])->get();
        $user = User::findOrfail(auth()->user()->id);
        $tasks = Task::with(['users', 'project', 'comments', 'attachments'])->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy('due_date','asc')->get();
        $projects = Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        return view('users.view-profile',
            array(
                'user' => $user,
                'activities' => $activities,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                'tasks' => $tasks,
                'projects' => $projects,
                
            )
        );
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $users = User::where('name', 'like', '%' . $query . '%')
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
