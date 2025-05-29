<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    //
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
       public function index() {
        return Task::with(['users', 'project', 'comments', 'attachments'])->get();
    }

    public function store(Request $request) {
        $task = Task::create($request->only('project_id', 'title', 'description'));
        return response()->json($task);
    }

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
