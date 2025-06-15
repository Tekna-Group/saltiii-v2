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
        return Project::with(['users'])->get();
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
/**
 * @OA\Put(
 *     path="/api/projects/{id}",
 *     tags={"Projects"},
 *     summary="Update a specific project",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the project to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "description"},
 *             @OA\Property(property="name", type="string", example="New Project Name"),
 *             @OA\Property(property="description", type="string", example="Updated project description")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Project updated successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $project = Project::findOrFail($id);
        $project->update($request->only('name', 'description'));
    
        return response()->json($project);
    }
/**
 * @OA\Delete(
 *     path="/api/projects/{id}",
 *     tags={"Projects"},
 *     summary="Soft delete a specific project",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the project to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Project soft deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function destroy($id) {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project soft deleted']);
    }
/**
 * @OA\Patch(
 *     path="/api/projects/{id}/complete",
 *     tags={"Projects"},
 *     summary="Mark a project as complete",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the project to mark complete",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Project marked as complete"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function complete($id)
    {
        $project = Project::findOrFail($id);
        $project->completed = true;
        $project->save();
    
        return response()->json($project);
    }
    /**
 * @OA\Post(
 *     path="/api/projects/{id}/assign-users",
 *     tags={"Projects"},
 *     summary="Assign multiple users to a project",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the project",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_ids"},
 *             @OA\Property(
 *                 property="user_ids",
 *                 type="array",
 *                 description="Array of user IDs to assign",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Users successfully assigned to project",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Users assigned successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function assignUsers(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $project = Project::findOrFail($id);
        $project->users()->sync($request->user_ids);
    
        return response()->json(['message' => 'Users assigned successfully']);
    }
    // public function assignTeam(Request $request, $id) {
    //     $project = Project::findOrFail($id);
    //     $project->team_id = $request->team_id;
    //     $project->save();
    //     return response()->json(['message' => 'Team assigned.']);
    // }
   
}
