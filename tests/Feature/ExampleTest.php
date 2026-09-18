<?php

use App\Models\User;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('login page can be rendered without errors', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Login JARA');
});

test('register page can be rendered without errors', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Register JARA');
});

test('a user can authenticate via login form', function () {
    $user = User::factory()->create([
        'email' => 'lintang@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'lintang@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('lists.index'));
    $this->assertAuthenticatedAs($user);
});
