<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    //
    public function index()
    {
        // Fetch all projects from the database
        // $projects = \App\Models\Project::all();

        // Return the view with the projects data
        return view('apiKeys.index');
    }
}
