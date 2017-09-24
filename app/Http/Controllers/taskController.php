<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\createTask;
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
}
