<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('project detail shows members and calculated task progress', function () {
    $project = Project::factory()->create(['name' => 'Skripsi JARA']);
    $member = User::factory()->create(['name' => 'Hana']);
    $project->members()->attach($member);
    Task::factory()->for($project)->create(['status' => 'completed']);
    Task::factory()->for($project)->create(['status' => 'completed']);
    Task::factory()->for($project)->create(['status' => 'pending']);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('Hana')
        ->assertSee('67%')
        ->assertSee('2 of 3 tasks completed');
});

test('a user can be added once as a project member', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $this->post(route('projects.members.store', $project), ['user_id' => $user->id])
        ->assertRedirect(route('projects.show', $project));

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $this->post(route('projects.members.store', $project), ['user_id' => $user->id])
        ->assertSessionHasErrors('user_id');

    expect($project->members()->count())->toBe(1);
});

test('removing a member does not delete the project tasks', function () {
    $project = Project::factory()->create();
    $member = User::factory()->create();
    $task = Task::factory()->for($project)->create();
    $project->members()->attach($member);

    $this->delete(route('projects.members.destroy', [$project, $member]))
        ->assertRedirect(route('projects.show', $project));

    $this->assertDatabaseMissing('project_user', [
        'project_id' => $project->id,
        'user_id' => $member->id,
    ]);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('a project without tasks has zero progress', function () {
    $project = Project::factory()->create();

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('0%')
        ->assertSee('0 of 0 tasks completed');
});

test('a project with only completed tasks has one hundred percent progress', function () {
    $project = Project::factory()->create();
    Task::factory()->count(2)->for($project)->create(['status' => 'completed']);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('100%')
        ->assertSee('2 of 2 tasks completed');
});
