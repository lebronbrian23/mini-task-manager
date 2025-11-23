<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a list of tasks page.
     * @return \Inertia\Response
     */
    public function index()
    {
        return inertia::render('tasks/ListTasks');
    }

    /**
     *  Get a list of Tasks
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasks($user_id = null)
    {

        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        // If user_id is provided, verify authorization
        if (!is_null($user_id)) {
            // Non-admins can only view their own tasks
            if ($user->id !== (int)$user_id && !$user->hasRole('admin') ) {
                abort(403, 'You can only view your own tasks.');
            }
            $tasks = Task::where('user_id', $user_id)->get();
        } else {
            // If no user_id provided
            if ($user->hasRole('admin')) {
                // Admins can see all tasks
                $tasks = Task::all();
            } else {
                // Regular users only see their own tasks
                $tasks = Task::where('user_id', $user->id)->get();
            }
        }

        return $tasks;
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia::render('tasks/AddTaskForm');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {

        $this->authorize('create', Task::class);
        $request->validated();

        $task = new Task();
        $task->name = $request->name;
        $task->description = $request->description;
        $task->user_id = $request->user_id;
        $task->save();

        return response()->json(['tasks' => $task ,'message' => 'Task added'] , 200);
    }

    /**
     * Show a Task
     * @param $id
     * @return mixed
     */
    public function getTask($id)
    {
        $task = Task::findorfail($id);
        $this->authorize('view', $task);
        return $task;
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return inertia::render('tasks/ShowTask',['task_id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param Task $task
     * @return \Inertia\Response
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return inertia::render('tasks/EditTaskForm');
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {

        $update_task = Task::where('id', $task->id)->first();

        $this->authorize('update', $update_task);

        $update_task->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ? : 'pending'
        ]);

        if ( $update_task->status === 'completed' ) {
            log::info('Task completed email dispatched here!');
            TaskCompleted::dispatch($update_task);
        }

        return response()->json(['message' => 'Task updated.' ,'tasks' => $update_task ], 200);
    }

    /**
     * Soft delete tasks.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::findorfail($id);

        $this->authorize('delete', $task);
        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Task deleted.' ], 200);
        } else {
            return response()->json(['message' => 'Task not found.' ], 200);
        }

    }

    /**
     *  Restore soft deleted tasks
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $task = Task::withTrashed()->findorfail($id);

        $this->authorize('restore', $task);

        $task->restore();

        return response()->json(['message' => 'Task restored.' ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_permanently($id)
    {
        $task = Task::findorfail($id);

        $this->authorize('forceDelete', $task);

        if ($task) {
            //soft delete
            $task->delete();

            // then force delete
            $task->forcedelete();

            return response()->json(['message' => 'Task deleted permanently.' ], 200);
        } else {
            return response()->json(['message' => 'Task not found.' ], 200);
        }

    }

}
