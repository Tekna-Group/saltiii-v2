<?php
/**
 * @OA\Info(
 *     title="Saltiii API",
 *     version="1.0.0",
 *     description="Task and Project Management API"
 * ),
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST
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
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/projects",
 *     tags={"Projects"},
 *     summary="Get all projects",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of projects"
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
 *      security={{"bearerAuth":{}}},
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
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }
    $project = new Project;
    $project->name = $request->name;
    $project->description = $request->description;
    $project->user_id = auth()->user()->id;
    $project->save();
    // $project = Project::create($validator->validated());

    return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
}
/**
 * @OA\Get(
 *     path="/api/projects/{id}",
 *     tags={"Projects"},
 *     summary="View a specific project",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the project",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Project details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function show($id) {
        return Project::with(['users'])->findOrFail($id);
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
