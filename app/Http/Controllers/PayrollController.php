<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    //
      public function index()
    {
        // Fetch all projects from the database
        // $projects = \App\Models\Project::all();

        // Return the view with the projects data
        return view('payroll.index');
    }
}
