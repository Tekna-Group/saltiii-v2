<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ProjectDiagram;
class ProjectDiagramController extends Controller
{
    //
    public function index($project_id)
    {
        $diagrams = ProjectDiagram::where('project_id', $project_id)->get();
        return view('diagrams.index', compact('diagrams', 'project_id'));
    }

    public function editor($id)
    {
        $diagram = ProjectDiagram::findOrFail($id);
        return view('diagrams.editor', compact('diagram'));
    }
    public function view($id)
    {
        $diagram = ProjectDiagram::findOrFail($id);
        return view('diagrams.view', compact('diagram'));

    }
    public function store(Request $request)
    {
        $diagram = ProjectDiagram::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'diagram_json' => '{}'
        ]);

        return redirect('/diagram/editor/'.$diagram->id);
    }

    public function save(Request $request, $id)
    {
        $diagram = ProjectDiagram::findOrFail($id);
        $diagram->diagram_json = $request->diagram_json;
        $diagram->save();

        return response()->json(['success' => true]);
    }
    public function lists()
    {
        $diagrams = ProjectDiagram::all();
        return view('diagrams.list', compact('diagrams'));
    }
    public function replicate($id)
    {
        $project = Project::findOrFail($id);

        // Allow only if published or owner
        if (!$project->publish && $project->created_by != auth()->id()) {
            abort(403);
        }

        $newProject = $project->replicate();
        $newProject->name = $project->name . ' (Copy)';
        $newProject->created_by = auth()->id();
        $newProject->publish = 0;
        $newProject->save();

        return redirect()->route('projects.edit', $newProject->id)
            ->with('success', 'Workflow duplicated!');
    }
}
