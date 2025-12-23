<?php

declare(strict_types=1);

use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows user to login with valid credentials', function () {
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
                'token',
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Login successful',
        ]);

    expect($response->json('data.token'))->not->toBeEmpty();
});

it('prevents login with invalid credentials', function () {
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
            'error'   => [
                'email' => ['The provided credentials are incorrect.'],
            ],
        ]);
});

it('requires email and password for login', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('validates email format', function () {
    $response = $this->postJson('/api/v1/login', [
        'email'    => 'invalid-email',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('handles non-existent user login attempt', function () {
    $response = $this->postJson('/api/v1/login', [
        'email'    => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
});

it('prevents login with empty password', function () {
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => 'test@example.com',
        'password' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('creates valid sanctum token on successful login', function () {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => 'test@example.com',
        'password' => 'password123',
    ]);

    $token = $response->json('data.token');

    expect($token)->toBeString();
    expect(strlen($token))->toBeGreaterThan(10);

    $authenticatedResponse = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/v1/deposit', [
        'payer'  => $user->id,
        'payee'  => $user->id,
        'amount' => 1000,
    ]);

    expect($authenticatedResponse->status())->not->toBe(401);
});
