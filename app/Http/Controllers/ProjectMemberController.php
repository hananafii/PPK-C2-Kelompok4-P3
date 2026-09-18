<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    public function legacyStore(Request $request, Project $project): RedirectResponse
    {
        $validated = $this->validateMember($request, $project);
        $project->members()->attach($validated['user_id']);

        return to_route('projects.show', $project)->with('success', 'Member berhasil ditambahkan.');
    }

    public function legacyDestroy(Project $project, User $user): RedirectResponse
    {
        $project->members()->detach($user->id);

        return to_route('projects.show', $project)->with('success', 'Member berhasil dihapus.');
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->owner_id === auth()->id(), 403);
        $validated = $this->validateMember($request, $project);
        $project->members()->attach($validated['user_id']);

        return to_route('lists.show', $project)->with('success', 'Member berhasil ditambahkan.');
    }

    private function validateMember(Request $request, Project $project): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id'),
                Rule::unique('project_user', 'user_id')
                    ->where(fn (Builder $query) => $query->where('project_id', $project->id)),
            ],
        ]);
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        abort_unless($project->owner_id === auth()->id(), 403);
        $project->members()->detach($user->id);

        return to_route('lists.show', $project)->with('success', 'Member berhasil dihapus.');
    }
}
