<?php
/**
 * @OA\Info(
 *     title="Saltiii API",
 *     version="1.0.0",
 *     description="Task and Project Management API"
 * )
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Project;
use App\Task;
use App\User;
use App\Team;
use App\Comment;
use App\Attachment;
use App\Activity;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Get all projects",
     *     @OA\Response(
     *         response=200,
     *         description="List of projects",
     *       
     *     )
     * )
     */
  public function index() {
    // dd('renz');
        return Project::with(['users', 'team'])->get();
    }
  /**
 * @OA\Post(
 *     path="/api/projects",
 *     tags={"Projects"},
 *     summary="Create a new project",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "description"},
 *             @OA\Property(property="name", type="string", example="Website Redesign"),
 *             @OA\Property(property="description", type="string", example="A project to redesign the company website")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Project created successfully"
 *     )
 * )
 */
    public function store(Request $request) {
        $project = Project::create($request->only('name', 'description'));
        return response()->json($project);
    }

    public function show($id) {
        return Project::with(['users', 'team'])->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $project = Project::findOrFail($id);
        $project->update($request->only('name', 'description'));
        return response()->json($project);
    }

    public function destroy($id) {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project soft deleted']);
    }

    public function complete($id) {
        $project = Project::findOrFail($id);
        $project->status = 'complete';
        $project->save();
        return response()->json($project);
    }

    public function assignUsers(Request $request, $id) {
        $project = Project::findOrFail($id);
        $project->users()->sync($request->user_ids);
        return response()->json(['message' => 'Users assigned.']);
    }

    public function assignTeam(Request $request, $id) {
        $project = Project::findOrFail($id);
        $project->team_id = $request->team_id;
        $project->save();
        return response()->json(['message' => 'Team assigned.']);
    }
   
}
