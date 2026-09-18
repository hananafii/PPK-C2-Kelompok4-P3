<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function legacyShow(Project $project): View
    {
        $project->load(['members:id,name', 'tasks:id,project_id,status']);
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $progress = $totalTasks === 0 ? 0 : (int) round(($completedTasks / $totalTasks) * 100);
        $availableUsers = User::query()->whereDoesntHave('projects', fn ($query) => $query->whereKey($project->id))->orderBy('name')->get(['id', 'name']);

        return view('projects.show', compact('availableUsers', 'completedTasks', 'progress', 'project', 'totalTasks'));
    }

    public function index(): View
    {
        $projects = Project::query()->where('owner_id', auth()->id())->orWhereHas('members', fn ($query) => $query->whereKey(auth()->id()))->withCount('tasks')->get();

        return view('lists.index', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create(['name' => $request->validate(['name' => ['required', 'max:255']])['name'], 'owner_id' => auth()->id()]);
        $project->members()->syncWithoutDetaching([auth()->id()]);

        return to_route('lists.show', $project);
    }

    public function show(Project $project): View
    {
        $project->load([
            'members:id,name',
            'tasks:id,project_id,status',
        ]);

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $progress = $totalTasks === 0
            ? 0
            : (int) round(($completedTasks / $totalTasks) * 100);

        abort_unless($project->owner_id === auth()->id() || $project->members()->whereKey(auth()->id())->exists(), 403);
        $availableUsers = User::query()
            ->whereDoesntHave('projects', fn ($query) => $query->whereKey($project->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('lists.show', compact(
            'availableUsers',
            'completedTasks',
            'progress',
            'project',
            'totalTasks',
        ));
    }
}
