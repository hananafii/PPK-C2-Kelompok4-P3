<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskList\TaskListStoreRequest;
use App\Http\Requests\TaskList\TaskListUpdateRequest;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('members', function ($mq) use ($user) {
                        $mq->where('users.id', $user->id);
                    });
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
        } elseif ($request->input('filter') === 'shared') {
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
     */
    public function store(TaskListStoreRequest $request): RedirectResponse
    {
        $taskList = TaskList::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('task-lists.show', $taskList)
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

        // Calculate statistics for this list
        $totalCount = $taskList->tasks()->count();
        $completedCount = $taskList->tasks()->where('status', 'Completed')->count();
        $inProgressCount = $taskList->tasks()->where('status', 'In Progress')->count();
        $pendingCount = $taskList->tasks()->where('status', 'Pending')->count();
        $progress = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;

        // Potential members to invite (excluding owner and existing members)
        $existingMemberIds = $taskList->members->pluck('id')->push($taskList->user_id);
        $availableUsers = User::whereNotIn('id', $existingMemberIds)->orderBy('name')->get();

        // All users who can be assigned tasks in this list (owner + members)
        $assignableUsers = $taskList->members->push($taskList->owner)->unique('id')->values();

        return view('task-lists.show', compact(
            'taskList',
            'tasks',
            'totalCount',
            'completedCount',
            'inProgressCount',
            'pendingCount',
            'progress',
            'availableUsers',
            'assignableUsers'
        ));
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $taskList): View
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $taskList->isOwnedBy($user)) {
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

        if (! $user->isAdmin() && ! $taskList->isOwnedBy($user)) {
            abort(403, 'Only the owner or an administrator can update this task list.');
        }

        $taskList->update($request->only('name', 'description'));

        return redirect()->route('task-lists.show', $taskList)
            ->with('success', 'Task list updated successfully!');
    }

    /**
     * Remove the specified task list from storage.
     */
    public function destroy(TaskList $taskList): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $taskList->isOwnedBy($user)) {
            abort(403, 'Only the owner or an administrator can delete this task list.');
        }

        $name = $taskList->name;
        $taskList->delete();

        return redirect()->route('task-lists.index')
            ->with('success', 'Task list "'.$name.'" was deleted.');
    }

    /**
     * Add a member to the task list.
     */
    public function addMember(Request $request, TaskList $taskList): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $taskList->isOwnedBy($user)) {
            abort(403, 'Only the owner or an administrator can invite members to this task list.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $inviteeId = (int) $validated['user_id'];

        if ($inviteeId === (int) $taskList->user_id) {
            return back()->with('error', 'The list owner is already part of the list.');
        }

        if ($taskList->members()->where('users.id', $inviteeId)->exists()) {
            return back()->with('error', 'This user is already a member of this list.');
        }

        $taskList->members()->attach($inviteeId);
        $invitedUser = User::find($inviteeId);

        return back()->with('success', $invitedUser->name.' has been invited to the task list!');
    }

    /**
     * Remove a member from the task list.
     */
    public function removeMember(TaskList $taskList, User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin() && ! $taskList->isOwnedBy($currentUser)) {
            abort(403, 'Only the owner or an administrator can remove members.');
        }

        $taskList->members()->detach($user->id);

        return back()->with('success', $user->name.' has been removed from the task list.');
    }

    /**
     * Display the dedicated Member Management page for the task list.
     */
    public function members(TaskList $taskList): View
    {
        $user = Auth::user();

        if (! $taskList->canAccess($user)) {
            abort(403, 'You do not have permission to view members of this task list.');
        }

        $taskList->load(['owner', 'members.assignedTasks' => function ($q) use ($taskList) {
            $q->where('task_list_id', $taskList->id);
        }]);

        $existingMemberIds = $taskList->members->pluck('id')->push($taskList->user_id);
        $availableUsers = User::whereNotIn('id', $existingMemberIds)->orderBy('name')->get();

        return view('task-lists.members', compact('taskList', 'availableUsers'));
    }
}
