<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskListMemberController extends Controller
{
    public function index(TaskList $taskList): View
    {
        $this->authorizeOwner($taskList);

        $taskList->load(['owner', 'members']);

        $excludedIds = $taskList->members->pluck('id')
            ->push($taskList->user_id);

        $availableUsers = User::whereNotIn('id', $excludedIds)
            ->orderBy('name')
            ->get();

        $totalTasks = $taskList->tasks()->count();
        $completedTasks = $taskList->tasks()
            ->whereIn('status', ['Completed', 'completed'])
            ->count();

        $progress = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        return view('task-lists.members', compact(
            'taskList',
            'availableUsers',
            'progress'
        ));
    }

    public function store(Request $request, TaskList $taskList): RedirectResponse
    {
        $this->authorizeOwner($taskList);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = (int) $validated['user_id'];

        if ($userId === (int) $taskList->user_id) {
            return back()->with('error', 'Owner tidak perlu ditambahkan sebagai member.');
        }

        if ($taskList->members()->where('users.id', $userId)->exists()) {
            return back()->with('error', 'User sudah menjadi member.');
        }

        $taskList->members()->attach($userId);

        return back()->with('success', 'Member berhasil ditambahkan.');
    }

    public function destroy(TaskList $taskList, User $user): RedirectResponse
    {
        $this->authorizeOwner($taskList);

        $taskList->members()->detach($user->id);

        return back()->with('success', 'Member berhasil dihapus.');
    }

    private function authorizeOwner(TaskList $taskList): void
    {
        abort_unless(
            (int) $taskList->user_id === (int) Auth::id(),
            403
        );
    }
}
