<?php

declare(strict_types=1);

use App\Domains\Transaction\Enums\TransactionType;
use Tests\Traits\ClearsCache;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, ClearsCache::class);

beforeEach(function () {
    $this->clearRedisCache();
});

it('allows admin to make deposit', function () {
    $admin        = $this->createAdmin();
    $externalFund = $this->createExternalFund();
    $user         = $this->createRegularUser();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => $externalFund->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'payer_user_id',
                'payee_user_id',
                'type',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Deposit processed successfully',
            'data'    => [
                'payer_user_id' => $externalFund->id,
                'payee_user_id' => $user->id,
                'type'          => TransactionType::DEPOSIT->value,
            ],
        ]);

    expect($response->json('data.payer_user_id'))->toBe($externalFund->id);
    expect($response->json('data.payee_user_id'))->toBe($user->id);
    expect($response->json('data.type'))->toBe('deposit');

    $this->assertDatabaseHas('transactions', [
        'payer_user_id' => $externalFund->id,
        'payee_user_id' => $user->id,
        'type'          => 'deposit',
    ]);

    $this->assertDatabaseHas('credits', [
        'amount' => 10000,
    ]);
});

it('allows external fund user to make deposit', function () {
    $externalFund        = $this->createExternalFund();
    $anotherExternalFund = $this->createExternalFund();
    $user                = $this->createRegularUser();

    $response = $this->actingAs($externalFund)
        ->postJson('/api/v1/deposit', [
            'value' => 5000,
            'payer' => $anotherExternalFund->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Deposit processed successfully',
        ]);
});

it('prevents regular user from making deposit', function () {
    $user         = $this->createRegularUser();
    $externalFund = $this->createExternalFund();
    $payee        = $this->createRegularUser();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => $externalFund->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(403);
});

it('requires authentication for deposit', function () {
    $externalFund = $this->createExternalFund();
    $user         = $this->createRegularUser();

    $response = $this->postJson('/api/v1/deposit', [
        'value' => 10000,
        'payer' => $externalFund->id,
        'payee' => $user->id,
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

it('validates required fields for deposit', function () {
    $admin = $this->createAdmin();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['value', 'payer', 'payee']);
});

it('validates amount is positive for deposit', function () {
    $admin        = $this->createAdmin();
    $externalFund = $this->createExternalFund();
    $user         = $this->createRegularUser();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => -100,
            'payer' => $externalFund->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'Invalid deposit amount: -100. Amount must be greater than 0',
        ]);
});

it('validates payer and payee are different for deposit', function () {
    $admin = $this->createAdmin();
    $user  = $this->createRegularUser();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => $user->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'Cannot create deposit where payer and payee are the same user',
        ]);
});

it('validates users exist for deposit', function () {
    $admin = $this->createAdmin();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => 999,
            'payee' => 998,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'User with ID 999 not found',
        ]);
});

it('fails when payer is not external fund for deposit', function () {
    $admin       = $this->createAdmin();
    $regularUser = $this->createRegularUser();
    $payee       = $this->createRegularUser();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => $regularUser->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => "Payer with role 'user' cannot be used for deposits. Only external_found users can be payers",
        ]);
});

it('fails when payee is external fund for deposit', function () {
    $admin               = $this->createAdmin();
    $externalFund        = $this->createExternalFund();
    $anotherExternalFund = $this->createExternalFund();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/deposit', [
            'value' => 10000,
            'payer' => $externalFund->id,
            'payee' => $anotherExternalFund->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => "Payee with role 'external_found' cannot recive deposits.",
        ]);
});
