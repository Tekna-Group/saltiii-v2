<?php

namespace App\Http\Controllers;

use App\WorkspaceSetting;
use Illuminate\Http\Request;

class WorkspaceSettingController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $settings = WorkspaceSetting::firstOrNew(['id' => 1]);
        $settings->company_name = $settings->company_name ?: config('app.name', 'SALTiii Workspace');
        $settings->timezone = $settings->timezone ?: config('app.timezone', 'Asia/Manila');
        $settings->workweek_start = $settings->workweek_start ?: 'Monday';
        $settings->standard_hours = $settings->standard_hours ?: 8;
        $settings->currency = $settings->currency ?: 'PHP';
        $settings->date_format = $settings->date_format ?: 'M j, Y';
        $settings->leave_approval_role = $settings->leave_approval_role ?: 'Project Lead';

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'company_name' => 'required|string|max:120',
            'timezone' => 'required|string|max:80',
            'workweek_start' => 'required|in:Monday,Sunday,Saturday',
            'standard_hours' => 'required|numeric|min:1|max:24',
            'currency' => 'required|in:PHP,USD,USDC',
            'date_format' => 'required|in:M j, Y,d M Y,Y-m-d',
            'leave_approval_role' => 'required|in:Project Lead,Admin',
        ]);

        $validated['require_time_notes'] = $request->has('require_time_notes');
        $validated['weekly_summary'] = $request->has('weekly_summary');
        $validated['updated_by'] = auth()->id();

        WorkspaceSetting::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Workspace settings saved.');
    }

    private function authorizeAdmin()
    {
        abort_unless(auth()->user()->role === 'Admin', 403);
    }
}
