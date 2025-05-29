<?php

namespace App\Http\Controllers\Api;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
class TaskController extends Controller
{



    /**
 * @OA\Get(
 *     path="/api/tasks",
 *     tags={"Tasks"},
 *     summary="Get all tasks",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all"
 *     )
 * )
 */
    public function index() {
        return Task::with(['users', 'project', 'comments', 'attachments'])->get();
    }
/**
 * @OA\Get(
 *     path="/api/projects/{project_id}/tasks",
 *     tags={"Tasks"},
 *     summary="Get all tasks for a specific project",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="project_id",
 *         in="path",
 *         required=true,
 *         description="ID of the project",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of tasks for the project",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="project_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Sample Task"),
 *             @OA\Property(property="description", type="string", example="Task details here")
 *         ))
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Project not found"
 *     )
 * )
 */
    public function getTasksByProject($project_id)
    {
        $project = Project::find($project_id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $tasks = Task::where('project_id', $project_id)->get();

        return response()->json($tasks);
    }
/**
 * @OA\Post(
 *     path="/api/tasks",
 *     tags={"Tasks"},
 *     summary="Create a new task",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"project_id", "title", "description"},
 *             @OA\Property(property="project_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Fix login bug"),
 *             @OA\Property(property="description", type="string", example="Resolve the 500 error on login form")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="project_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Fix login bug"),
 *             @OA\Property(property="description", type="string", example="Resolve the 500 error on login form")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $task = Task::create($request->only('project_id', 'title', 'description'));
    
        return response()->json($task);
    }
/**
     * @OA\Post(
     *     path="/api/tasks/{id}/upload-attachment",
     *     tags={"Tasks"},
     *     summary="Upload an attachment to a task",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="file", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully"
     *     ),
     * )
     */
    
    public function uploadAttachment(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $request->validate(['file' => 'required|file|max:10240']);

        $path = $request->file('file')->store('attachments');

        $task->attachments()->create([
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'File uploaded', 'path' => $path]);
    }
    public function logActivity(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hours' => 'required|numeric|min:0.1',
            'description' => 'nullable|string',
        ]);

        $task->activities()->create($data);

        return response()->json(['message' => 'Activity logged']);
    }
     

/**
 * @OA\Get(
 *     path="/api/tasks/{id}",
 *     tags={"Tasks"},
 *     summary="View a specific task with related users, comments, and attachments",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the task",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task details",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Task Title"),
 *             @OA\Property(property="description", type="string", example="Task description here"),
 *             @OA\Property(property="users", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="comments", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="attachments", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Task not found"
 *     )
 * )
 */
    public function show($id) {
        return Task::with(['users', 'comments', 'attachments'])->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $task = Task::findOrFail($id);
        $task->update($request->only('title', 'description'));
        return response()->json($task);
    }

    public function destroy($id) {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function complete($id) {
        $task = Task::findOrFail($id);
        $task->status = 'complete';
        $task->save();
        return response()->json($task);
    }

    public function setDeadline(Request $request, $id) {
        $task = Task::findOrFail($id);
        $task->deadline = $request->deadline;
        $task->save();
        return response()->json($task);
    }

    public function assignUsers(Request $request, $id) {
        $task = Task::findOrFail($id);
        $task->users()->sync($request->user_ids);
        return response()->json(['message' => 'Users assigned.']);
    }

    public function addComment(Request $request, $id) {
        $task = Task::findOrFail($id);
        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);
        return response()->json($comment);
    }

}
