<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\assignTaskDeadline;
use App\Http\Requests\Tasks\createTask;
use App\Http\Requests\Tasks\toggleTaskStatus;
use App\Task;

class taskController extends Controller
{
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
}
