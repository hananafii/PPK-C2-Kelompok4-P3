<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskList\TaskListStoreRequest;
use App\Http\Requests\TaskList\TaskListUpdateRequest;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TaskListController extends Controller
{
    /**
     * Display a listing of task lists.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = TaskList::with(['owner', 'members', 'tasks'])->withCount('tasks');

        if (! empty($user) && ! (method_exists($user, 'isAdmin') && $user->isAdmin()) && empty($user->is_admin)) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if (Schema::hasTable('task_list_user')) {
                    $q->orWhereHas('members', function ($mq) use ($user) {
                        $mq->where('users.id', $user->id);
                    });
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->input('filter') === 'owned') {
            $query->where('user_id', $user->id);
        } elseif ($request->input('filter') === 'shared' && Schema::hasTable('task_list_user')) {
            $query->where('user_id', '!=', $user->id)
                ->whereHas('members', function ($mq) use ($user) {
                    $mq->where('users.id', $user->id);
                });
        }

        $taskLists = $query->latest()->paginate(9)->withQueryString();

        return view('task-lists.index', compact('taskLists'));
    }

    /**
     * Show the form for creating a new task list.
     */
    public function create(): View
    {
        return view('task-lists.create');
    }

    /**
     * Store a newly created task list in storage.
     * Automatically assigns the authenticated user as the owner.
     */
    public function store(TaskListStoreRequest $request): RedirectResponse
    {
        $taskList = DB::transaction(function () use ($request) {
            return TaskList::create([
                'user_id' => Auth::id(),
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
            ]);
        });

        return redirect()->route('task-lists.index')
            ->with('success', 'Task list "'.$taskList->name.'" created successfully!');
    }

    /**
     * Display the specified task list.
     */
    public function show(Request $request, TaskList $taskList): View
    {
        $user = Auth::user();

        if (! $taskList->canAccess($user)) {
            abort(403, 'You do not have permission to view this task list.');
        }

        $tasksQuery = $taskList->tasks()->with(['creator', 'assignee']);

        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $tasksQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $tasksQuery->orderByRaw("CASE status WHEN 'In Progress' THEN 1 WHEN 'Pending' THEN 2 WHEN 'Completed' THEN 3 ELSE 4 END")
            ->orderBy('deadline', 'asc')
            ->paginate(15)
            ->withQueryString();

        $totalCount = $taskList->tasks()->count();
        $completedCount = $taskList->tasks()->whereIn('status', ['Completed', 'completed'])->count();
        $inProgressCount = $taskList->tasks()->whereIn('status', ['In Progress', 'in progress'])->count();
        $pendingCount = $taskList->tasks()->whereIn('status', ['Pending', 'pending'])->count();
        $progress = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;

        $existingMemberIds = collect([$taskList->user_id]);
        if (Schema::hasTable('task_list_user')) {
            $existingMemberIds = $taskList->members->pluck('id')->push($taskList->user_id);
        }
        $availableUsers = User::whereNotIn('id', $existingMemberIds)->orderBy('name')->get();

        return view('task-lists.show', compact(
            'taskList',
            'tasks',
            'totalCount',
            'completedCount',
            'inProgressCount',
            'pendingCount',
            'progress',
            'availableUsers'
        ));
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $taskList): View
    {
        $user = Auth::user();

        if (! (method_exists($user, 'isAdmin') && $user->isAdmin()) && empty($user->is_admin) && ! $taskList->isOwnedBy($user)) {
            abort(403, 'Only the owner or an administrator can edit this task list.');
        }

        return view('task-lists.edit', compact('taskList'));
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(TaskListUpdateRequest $request, TaskList $taskList): RedirectResponse
    {
        $user = Auth::user();

        if (! (method_exists($user, 'isAdmin') && $user->isAdmin()) && empty($user->is_admin) && ! $taskList->isOwnedBy($user)) {
            abort(403, 'Only the owner or an administrator can update this task list.');
        }

        DB::transaction(function () use ($taskList, $request) {
            $taskList->update($request->only('name', 'description'));
        });

        return redirect()->route('task-lists.index')
            ->with('success', 'Task list updated successfully!');
    }

    /**
     * Remove the specified task list from storage.
     * All delete operations (tasks, memberships, task list) are atomic using DB::transaction.
     */
    public function destroy(TaskList $taskList): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validate ownership: only the owner can delete the task list
        if (! $user || (int) $taskList->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized. Only the owner can delete this task list.');
        }

        $name = $taskList->name;

        // Atomic multi-step deletion
        DB::transaction(function () use ($taskList) {
            // Step 1: Delete all related tasks
            $taskList->tasks()->delete();

            // Step 2: Delete all collaboration / membership records
            if (Schema::hasTable('task_list_user')) {
                $taskList->members()->detach();
            }

            // Step 3: Delete the task list itself
            $taskList->delete();
        });

        return redirect()->route('task-lists.index')
            ->with('success', 'Task list "'.$name.'" and all its contents were permanently deleted.');
    }
}
