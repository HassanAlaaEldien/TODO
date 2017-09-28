<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\assignTaskDeadline;
use App\Http\Requests\Tasks\attachFileToTask;
use App\Http\Requests\Tasks\createTask;
use App\Http\Requests\Tasks\toggleTaskStatus;
use App\Task;
use Illuminate\Support\Facades\Storage;

class taskController extends Controller
{
    /* Start Task CRUD Operation */

    public function create(createTask $request, Task $task)
    {
        $task->add($request->all());

        return response()->json(['success' => true], 201);
    }

    public function edit(createTask $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->edit($request->all());

        return response()->json(['success' => true], 200);
    }

    public function delete(Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->delete();

        return response()->json(['success' => true], 200);
    }

    /* End Task CRUD Operation */
    
    public function assignDeadline(assignTaskDeadline $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        if ($task->checkDeadlineAvailability($request['deadline']))
            return response()->json(['success' => false, 'message' => 'please enter valid date for deadline.'], 422);

        $task->assign($request->all());

        return response()->json(['success' => true], 201);
    }

    public function toggleStatus(toggleTaskStatus $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $task->update([
            'status' => $request['status']
        ]);

        return response()->json(['success' => true], 200);
    }

    public function attachFile(attachFileToTask $request, Task $task)
    {
        if (!$task->checkUserAccessibility())
            return response()->json(['success' => false], 401);

        $path = Storage::disk('Tasks')->putFile('files', $request->file('file'));

        $task->attachFile($path);

        return response()->json(['success' => true], 200);
    }
}
