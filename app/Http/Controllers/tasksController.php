<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\assignTaskDeadline;
use App\Http\Requests\Tasks\attachFileToTask;
use App\Http\Requests\Tasks\createTask;
use App\Http\Requests\Tasks\toggleTaskStatus;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class tasksController extends Controller
{

    /**
     * Get All Public Tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tasks = Task::where('status', 'public')->get();

        return response()->json(['success' => true, 'tasks' => $tasks]);
    }

    /**
     * Create Task Process.
     *
     * @param createTask $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(createTask $request, Task $task)
    {
        $task->add($request->all());

        return response()->json(['success' => true], 201);
    }

    /**
     * Edit Task Process.
     *
     * @param createTask $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(createTask $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->edit($request->all());

        return response()->json(['success' => true], 200);
    }

    /**
     * Delete Task Process.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->delete();

        return response()->json(['success' => true], 200);
    }

    /**
     * Assigning Deadline To Task.
     *
     * @param assignTaskDeadline $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignDeadline(assignTaskDeadline $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        if ($task->checkDeadlineAvailability($request['deadline']))
            return response()->json(['success' => false, 'message' => 'please enter valid date for deadline.'], 422);

        $task->assign($request->all());

        return response()->json(['success' => true], 201);
    }

    /**
     * Toggle Task Status From (Private) To (Public) And Vise Versa.
     *
     * @param toggleTaskStatus $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(toggleTaskStatus $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->update([
            'status' => $request['status']
        ]);

        return response()->json(['success' => true], 200);
    }

    /**
     * Attach File To Task.
     *
     * @param attachFileToTask $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachFile(attachFileToTask $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $path = Storage::disk('Tasks')->putFile('files', $request->file('file'));

        $task->attachFile($path);

        return response()->json(['success' => true], 200);
    }

    /**
     * Sending Invitation To Watch Private Tasks.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvitation(Request $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        Auth::user()->sendInvitation($request->user, $task);

        return response()->json(['success' => true], 200);
    }


    /**
     * Respond To Invitation.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function invitationResponse(Request $request, Task $task)
    {
        $authorized = Auth::user()->checkIfUserInvited($task);

        if (!$authorized)
            return response()->json(['success' => false], 403);

        Auth::user()->respondToInvitation($request->reply, $authorized, $task);

        return response()->json(['success' => true], 201);
    }
}
