<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('user can create task list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/task-lists', [
        'name' => 'Backend Sprint Tasks',
        'description' => 'All tasks related to backend API development',
    ]);

    $response->assertRedirect(route('task-lists.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('task_lists', [
        'name' => 'Backend Sprint Tasks',
        'description' => 'All tasks related to backend API development',
        'user_id' => $user->id,
    ]);
});

test('created task list automatically belongs to user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/task-lists', [
        'name' => 'Personal Checklist',
        'description' => 'My personal todo items',
    ]);

    $taskList = TaskList::where('name', 'Personal Checklist')->first();

    expect($taskList)->not->toBeNull()
        ->and((int) $taskList->user_id)->toBe((int) $user->id)
        ->and($taskList->isOwnedBy($user))->toBeTrue();
});

test('user cannot delete another user\'s task list', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Owner Private List',
        'description' => 'Owner only',
    ]);

    $response = $this->actingAs($otherUser)->delete("/task-lists/{$taskList->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('task_lists', [
        'id' => $taskList->id,
        'name' => 'Owner Private List',
    ]);
});

test('deleting task list deletes related tasks', function () {
    $owner = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Sprint Tasks',
        'description' => 'List with multiple tasks',
    ]);

    $task1 = Task::factory()->create([
        'task_list_id' => $taskList->id,
        'created_by' => $owner->id,
        'title' => 'Task One',
    ]);

    $task2 = Task::factory()->create([
        'task_list_id' => $taskList->id,
        'created_by' => $owner->id,
        'title' => 'Task Two',
    ]);

    $this->assertDatabaseHas('tasks', ['id' => $task1->id]);
    $this->assertDatabaseHas('tasks', ['id' => $task2->id]);

    $response = $this->actingAs($owner)->delete("/task-lists/{$taskList->id}");

    $response->assertRedirect(route('task-lists.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('task_lists', ['id' => $taskList->id]);
    $this->assertDatabaseMissing('tasks', ['id' => $task1->id]);
    $this->assertDatabaseMissing('tasks', ['id' => $task2->id]);
});

test('deleting task list deletes memberships', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Collaborative Project',
        'description' => 'Shared with team members',
    ]);

    if (Schema::hasTable('task_list_user')) {
        $taskList->members()->attach($member->id);

        $this->assertDatabaseHas('task_list_user', [
            'task_list_id' => $taskList->id,
            'user_id' => $member->id,
        ]);
    }

    $response = $this->actingAs($owner)->delete("/task-lists/{$taskList->id}");

    $response->assertRedirect(route('task-lists.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('task_lists', ['id' => $taskList->id]);

    if (Schema::hasTable('task_list_user')) {
        $this->assertDatabaseMissing('task_list_user', [
            'task_list_id' => $taskList->id,
            'user_id' => $member->id,
        ]);
    }
});

test('transaction rollback works when failure occurs', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $taskList = TaskList::create([
        'user_id' => $owner->id,
        'name' => 'Critical List',
        'description' => 'Must not be partially deleted',
    ]);

    $task = Task::factory()->create([
        'task_list_id' => $taskList->id,
        'created_by' => $owner->id,
        'title' => 'Critical Task',
    ]);

    if (Schema::hasTable('task_list_user')) {
        $taskList->members()->attach($member->id);
    }

    // Simulate an error on the last step of the transaction (TaskList deletion)
    TaskList::deleting(function () {
        throw new RuntimeException('Simulated failure during task list deletion');
    });

    try {
        $this->withoutExceptionHandling()
            ->actingAs($owner)
            ->delete("/task-lists/{$taskList->id}");
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toBe('Simulated failure during task list deletion');
    }

    // Assert that the transaction was rolled back completely:
    // TaskList still exists
    $this->assertDatabaseHas('task_lists', ['id' => $taskList->id]);
    // Related task still exists (not orphaned or deleted)
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    // Collaboration membership still exists
    if (Schema::hasTable('task_list_user')) {
        $this->assertDatabaseHas('task_list_user', [
            'task_list_id' => $taskList->id,
            'user_id' => $member->id,
        ]);
    }
});

test('invalid input is rejected', function () {
    $user = User::factory()->create();

    // 1. Missing name
    $response1 = $this->actingAs($user)->post('/task-lists', [
        'name' => '',
        'description' => 'Some description',
    ]);
    $response1->assertSessionHasErrors(['name']);

    // 2. Name exceeds 255 chars
    $response2 = $this->actingAs($user)->post('/task-lists', [
        'name' => str_repeat('A', 256),
        'description' => 'Valid description',
    ]);
    $response2->assertSessionHasErrors(['name']);

    // 3. Description exceeds 1000 chars
    $response3 = $this->actingAs($user)->post('/task-lists', [
        'name' => 'Valid Name',
        'description' => str_repeat('B', 1001),
    ]);
    $response3->assertSessionHasErrors(['description']);

    // Ensure no task list was created with invalid data
    $this->assertDatabaseMissing('task_lists', [
        'description' => str_repeat('B', 1001),
    ]);
});
