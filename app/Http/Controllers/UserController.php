<?php

namespace App\Http\Controllers;
use App\User;
use App\UserSalary;
use App\TaskActivity;
use App\Task;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
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
            'hourly_rate' => 'required|numeric|min:0',
            'wallet_address' => 'nullable|string|max:255',
            'wallet_network' => 'nullable|string|max:100',
            'stripe_account_id' => 'nullable|string|max:255|regex:/^acct_[A-Za-z0-9]+$/',
            'airwallex_beneficiary_id' => 'nullable|string|max:255',
        ]);

        $new_account = new User;
        $new_account->name = $request->name;
        $new_account->email = $request->email;
        $new_account->role = $request->role;
        $new_account->password = bcrypt($request->password);
        $new_account->status = "Active";
        $new_account->wallet_address = $request->wallet_address;
        $new_account->wallet_network = $request->wallet_network;
        $new_account->stripe_account_id = $request->stripe_account_id;
        $new_account->airwallex_beneficiary_id = $request->airwallex_beneficiary_id;
        $new_account->save();

        UserSalary::updateOrCreate(
            ['user_id' => $new_account->id, 'type' => 'hourly'],
            ['salary' => $request->hourly_rate]
        );

        Mail::to($new_account->email)->send(new WelcomeEmail($new_account));

        Alert::success('Successfully Saved', 'A welcome email has been sent.')->persistent('Dismiss');
        return back();
    }

    public function editUser(Request $request,$id)
    {
        // dd($request->all());
        $this->validate($request, [
            'email' => 'unique:users,email,' . $id,
            'hourly_rate' => 'nullable|numeric|min:0',
            'wallet_address' => 'nullable|string|max:255',
            'wallet_network' => 'nullable|string|max:100',
            'stripe_account_id' => 'nullable|string|max:255|regex:/^acct_[A-Za-z0-9]+$/',
            'airwallex_beneficiary_id' => 'nullable|string|max:255',
        ]);

        $account = User::where('id', $id)->first();
        $account->name = $request->name;
        $account->email = $request->email;
        $account->role = $request->role;
        $account->wallet_address = $request->wallet_address;
        $account->wallet_network = $request->wallet_network;
        $account->stripe_account_id = $request->stripe_account_id;
        $account->airwallex_beneficiary_id = $request->airwallex_beneficiary_id;
        $account->save();

        UserSalary::updateOrCreate(
            ['user_id' => $account->id, 'type' => 'hourly'],
            ['salary' => $request->hourly_rate ?: 0]
        );

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
