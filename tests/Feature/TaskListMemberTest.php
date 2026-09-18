<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;

test('owner can view task list members page and monitor progress', function () {
    $owner = User::factory()->create();
    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Team Task List',
        'description' => 'Collaboration list',
    ]);

    Task::factory()->create([
        'task_list_id' => $taskList->id,
        'created_by' => $owner->id,
        'status' => 'Completed',
    ]);
    Task::factory()->create([
        'task_list_id' => $taskList->id,
        'created_by' => $owner->id,
        'status' => 'Pending',
    ]);

    $response = $this->actingAs($owner)->get("/task-lists/{$taskList->id}/members");

    $response->assertOk();
    $response->assertSee('List Collaborators');
    $response->assertSee('50%'); // 1 completed out of 2 = 50%
});

test('owner can add member to task list', function () {
    $owner = User::factory()->create();
    $newMember = User::factory()->create();
    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Team Task List',
    ]);

    $response = $this->actingAs($owner)->post("/task-lists/{$taskList->id}/members", [
        'user_id' => $newMember->id,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('task_list_user', [
        'task_list_id' => $taskList->id,
        'user_id' => $newMember->id,
    ]);
});

test('owner cannot add themselves as member', function () {
    $owner = User::factory()->create();
    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Team Task List',
    ]);

    $response = $this->actingAs($owner)->post("/task-lists/{$taskList->id}/members", [
        'user_id' => $owner->id,
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('task_list_user', [
        'task_list_id' => $taskList->id,
        'user_id' => $owner->id,
    ]);
});

test('owner cannot add duplicate member', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Team Task List',
    ]);
    $taskList->members()->attach($member->id);

    $response = $this->actingAs($owner)->post("/task-lists/{$taskList->id}/members", [
        'user_id' => $member->id,
    ]);

    $response->assertSessionHas('error');
    expect($taskList->members()->count())->toBe(1);
});

test('owner can remove member from task list', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Team Task List',
    ]);
    $taskList->members()->attach($member->id);

    $response = $this->actingAs($owner)->delete("/task-lists/{$taskList->id}/members/{$member->id}");

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('task_list_user', [
        'task_list_id' => $taskList->id,
        'user_id' => $member->id,
    ]);
});

test('non-owner cannot manage task list members', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $newMember = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Private List',
    ]);

    // Non-owner cannot view members page
    $this->actingAs($otherUser)
        ->get("/task-lists/{$taskList->id}/members")
        ->assertStatus(403);

    // Non-owner cannot add members
    $this->actingAs($otherUser)
        ->post("/task-lists/{$taskList->id}/members", ['user_id' => $newMember->id])
        ->assertStatus(403);

    // Non-owner cannot remove members
    $taskList->members()->attach($newMember->id);
    $this->actingAs($otherUser)
        ->delete("/task-lists/{$taskList->id}/members/{$newMember->id}")
        ->assertStatus(403);
});
