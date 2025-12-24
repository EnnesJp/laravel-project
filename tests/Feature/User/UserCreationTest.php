<?php

use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a user with valid data', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '43861510014',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'document',
                    'role',
                ],
            ]);

    $this->assertDatabaseHas('users', [
        'name'     => $userData['name'],
        'email'    => $userData['email'],
        'document' => $userData['document'],
    ]);
});

it('fails to create user with invalid cpf', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '12345678900',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['document']);
});

it('fails to create user with weak password', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '98421523082',
        'password' => '123456',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422);
    $json = $response->json();
    expect($json['error'])->toBe('Password must be at least 8 characters long and contain letters, numbers, and symbols.');
});

it('fails to create user with duplicate email', function () {
    User::factory()->create(['email' => 'joao@example.com']);

    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '43861510014',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
});

it('fails to create user with duplicate document', function () {
    User::factory()->create(['document' => '43861510014']);

    $userData = [
        'name'     => 'Maria Silva',
        'email'    => 'maria@example.com',
        'document' => '43861510014',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['document']);
});

it('fails to create user with invalid email format', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'invalid-email',
        'document' => '98421523082',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['error'])->toBe('Invalid email: invalid-email');
});

it('fails to create user with missing required fields', function () {
    $userData = [
        'name' => 'João Silva',
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'document', 'password']);
});

it('fails to create user with invalid role', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '12345678901',
        'password' => 'Password123!',
        'role'     => 'invalid-role',
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
});

it('can create user with valid cnpj', function () {
    $userData = [
        'name'     => 'Empresa LTDA',
        'email'    => 'empresa@example.com',
        'document' => '11222333000181',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
            ]);

    $this->assertDatabaseHas('users', [
        'name'     => 'Empresa LTDA',
        'email'    => 'empresa@example.com',
        'document' => '11222333000181',
    ]);
});

it('formats document correctly in response', function () {
    $userData = [
        'name'     => 'João Silva',
        'email'    => 'joao@example.com',
        'document' => '43861510014',
        'password' => 'Password123!',
        'role'     => UserRole::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $userData);

    $response->assertStatus(201)
            ->assertJsonPath('data.document', '438.615.100-14');
});
