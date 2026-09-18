<?php

use App\Models\User;

test('admin can view user management page', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('User Management');
});

test('admin can create a new user account', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => 'New User Test',
        'email' => 'newuser@jara.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('admin.users'));
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@jara.com',
        'name' => 'New User Test',
    ]);
});

test('admin cannot create user with invalid data', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

test('admin can delete a user account', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['name' => 'User To Delete']);

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

    $response->assertRedirect(route('admin.users'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('admin cannot delete own account', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

    $response->assertStatus(422);
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('non-admin user is rejected from admin user management', function () {
    $regularUser = User::factory()->create(['is_admin' => false]);
    $targetUser = User::factory()->create();

    // Cannot view
    $this->actingAs($regularUser)->get('/admin/users')->assertStatus(403);

    // Cannot create
    $this->actingAs($regularUser)->post('/admin/users', [
        'name' => 'Hacker User',
        'email' => 'hacker@jara.com',
        'password' => 'password123',
    ])->assertStatus(403);

    // Cannot delete
    $this->actingAs($regularUser)->delete("/admin/users/{$targetUser->id}")->assertStatus(403);
});
