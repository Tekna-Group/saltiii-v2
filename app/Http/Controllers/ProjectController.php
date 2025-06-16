<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\ProjectUser;
use App\ProjectBoard;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{
    //

    public function index()
    {
        // Fetch all projects from the database
        // $projects = \App\Models\Project::all();
        $projects = Project::get();
        $users = User::get();
        // Return the view with the projects data
        return view('projects.index',
            array(
                'projects' => $projects,
                'users' => $users,
            )
        );
    }
    public function store(Request $request)
    {
        // Validate the request data
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new project instance
        $project = new Project();
        $project->name = $request->input('name');
        $project->description = $request->input('description');
        $project->status = $request->input('status');
        $project->user_id = auth()->user()->id; // Assuming the project is created by the authenticated user
        $project->save();
        // Handle file upload for project icon
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/projects'), $filename);
            $project->icon = 'uploads/projects/' . $filename;
            $project->save();
        } 
        // Attach team members to the project
        foreach($request->input('team_member') as $memberId) {
            $projectUser = new ProjectUser();
            $projectUser->project_id = $project->id;
            $projectUser->user_id = $memberId;
            $projectUser->save();
        }

        // Redirect back with success message
        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
        
    }

    public function view(Request $request,$id)
    {

        $project = Project::with('users','boards')->findOrfail($id);
        // Return the view with the projects data
    
        $users = User::get();
        return view('projects.view',
            array(
                'project' => $project,
                'users' => $users,
            )
        );
    }

    public function teamMember(Request $request,$id)
    {

        ProjectUser::where('project_id',$id)->delete();

          foreach($request->input('team_member') as $memberId) {
            $projectUser = new ProjectUser();
            $projectUser->project_id = $id;
            $projectUser->user_id = $memberId;
            $projectUser->save();
        }

            // Redirect back with success message
        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }

    public function boardProject(Request $request,$id)
    {
        $project = new ProjectBoard;
        $project->project_id = $id;
        $project->board = $request->boardName;
        $project->save();

         Alert::success('Successfully Encoded')->persistent('Dismiss');
        return back();
    }
}
