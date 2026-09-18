<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;

test('authenticated users can view the tasks index page', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);

    Task::factory()->count(3)->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->get('/tasks');

    $response->assertStatus(200);
    $response->assertSee('Tasks Overview');
});

test('users can create tasks inside their task list', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);

    $response = $this->actingAs($user)->post('/tasks', [
        'task_list_id' => $list->id,
        'title' => 'Write documentation',
        'description' => 'Document all API endpoints and models',
        'priority' => 'High',
        'status' => 'Pending',
        'deadline' => now()->addDays(5)->format('Y-m-d'),
        'assigned_to' => $user->id,
    ]);

    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('tasks', [
        'task_list_id' => $list->id,
        'title' => 'Write documentation',
        'priority' => 'High',
        'status' => 'Pending',
    ]);
});

test('task validation enforces required title, priority, status, and deadline', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);

    $response = $this->actingAs($user)->post('/tasks', [
        'task_list_id' => $list->id,
        'title' => '',
        'priority' => 'InvalidPriority',
        'status' => 'InvalidStatus',
        'deadline' => 'not-a-date',
    ]);

    $response->assertSessionHasErrors(['title', 'priority', 'status', 'deadline']);
});

test('users can view task details', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);
    $task = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
        'title' => 'Specific Task Title',
    ]);

    $response = $this->actingAs($user)->get("/tasks/{$task->id}");

    $response->assertStatus(200);
    $response->assertSee('Specific Task Title');
});

test('users can view task edit form', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);
    $task = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->get("/tasks/{$task->id}/edit");

    $response->assertStatus(200);
    $response->assertSee('Edit Task');
});

test('users can update task attributes', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);
    $task = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
        'title' => 'Old Title',
    ]);

    $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
        'task_list_id' => $list->id,
        'title' => 'Updated Task Title',
        'description' => 'Updated description content',
        'priority' => 'Low',
        'status' => 'In Progress',
        'deadline' => now()->addDays(10)->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('tasks.show', $task));
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Task Title',
        'priority' => 'Low',
        'status' => 'In Progress',
    ]);
});

test('users can update task status', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);
    $task = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
        'status' => 'Pending',
    ]);

    $response = $this->actingAs($user)->patch("/tasks/{$task->id}/status", [
        'status' => 'Completed',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'Completed',
    ]);
});

test('users can filter tasks by priority and status', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);

    $urgentTask = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
        'title' => 'Urgent Server Bug',
        'priority' => 'High',
        'status' => 'Pending',
    ]);

    $routineTask = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
        'title' => 'Routine Weekly Cleaning',
        'priority' => 'Low',
        'status' => 'Completed',
    ]);

    // Filter by High priority
    $response = $this->actingAs($user)->get('/tasks?priority=High');
    $response->assertStatus(200);
    $response->assertSee('Urgent Server Bug');
    $response->assertDontSee('Routine Weekly Cleaning');

    // Filter by Completed status
    $responseCompleted = $this->actingAs($user)->get('/tasks?status=Completed');
    $responseCompleted->assertStatus(200);
    $responseCompleted->assertSee('Routine Weekly Cleaning');
    $responseCompleted->assertDontSee('Urgent Server Bug');
});

test('users can delete tasks in their list', function () {
    $user = User::factory()->create();
    $list = TaskList::create(['user_id' => $user->id, 'name' => 'General Tasks', 'description' => 'Test task list']);
    $task = Task::factory()->create([
        'task_list_id' => $list->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

    $response->assertRedirect(route('tasks.index'));
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('task helper methods isCompleted and isOverdue work accurately', function () {
    $taskCompleted = new Task(['status' => 'Completed']);
    expect($taskCompleted->isCompleted())->toBeTrue();

    $taskPending = new Task(['status' => 'Pending']);
    expect($taskPending->isCompleted())->toBeFalse();

    $overdueTask = new Task([
        'status' => 'Pending',
        'deadline' => now()->subDays(2),
    ]);
    expect($overdueTask->isOverdue())->toBeTrue();

    $futureTask = new Task([
        'status' => 'Pending',
        'deadline' => now()->addDays(2),
    ]);
    expect($futureTask->isOverdue())->toBeFalse();
});
