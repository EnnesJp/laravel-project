<?php

declare(strict_types=1);

use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Credit;
use App\Models\Transaction;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

function createUserWithBalance(UserRole $role, int $balance): \App\Models\User
{
    $user         = test()->createUser(['role' => $role]);
    $externalFund = test()->createExternalFund();

    $transaction = Transaction::create([
        'payer_user_id' => $externalFund->id,
        'payee_user_id' => $user->id,
        'type'          => TransactionType::DEPOSIT,
    ]);

    Credit::create([
        'transaction_id' => $transaction->id,
        'amount'         => $balance,
    ]);

    return $user;
}

it('allows user to make transfer with sufficient balance', function () {
    $payer = createUserWithBalance(UserRole::USER, 10000);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $payer->id,
            'payee' => $payee->id,
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
            'message' => 'Transfer processed successfully',
            'data'    => [
                'payer_user_id' => $payer->id,
                'payee_user_id' => $payee->id,
                'type'          => 'transfer',
            ],
        ]);

    expect($response->json('data.payer_user_id'))->toBe($payer->id);
    expect($response->json('data.payee_user_id'))->toBe($payee->id);
    expect($response->json('data.type'))->toBe('transfer');

    $this->assertDatabaseHas('transactions', [
        'payer_user_id' => $payer->id,
        'payee_user_id' => $payee->id,
        'type'          => 'transfer',
    ]);

    $this->assertDatabaseHas('credits', [
        'amount' => 5000,
    ]);

    $this->assertDatabaseHas('debits', [
        'amount' => 5000,
    ]);
});

it('allows admin to make transfer', function () {
    $admin = createUserWithBalance(UserRole::ADMIN, 15000);
    $payee = $this->createSeller();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/transfer', [
            'value' => 8000,
            'payer' => $admin->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer processed successfully',
        ]);
});

it('prevents seller from making transfer', function () {
    $seller = createUserWithBalance(UserRole::SELLER, 10000);
    $payee  = $this->createRegularUser();

    $response = $this->actingAs($seller)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $seller->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(403);
});

it('prevents external fund from making transfer', function () {
    $externalFund = $this->createExternalFund();
    $payee        = $this->createRegularUser();

    $response = $this->actingAs($externalFund)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $externalFund->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(403);
});

it('requires authentication for transfer', function () {
    $payer = $this->createRegularUser();
    $payee = $this->createRegularUser();

    $response = $this->postJson('/api/v1/transfer', [
        'value' => 5000,
        'payer' => $payer->id,
        'payee' => $payee->id,
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.',
        ]);
});

it('validates required fields for transfer', function () {
    $user = $this->createRegularUser();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/transfer', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['value', 'payer', 'payee']);
});

it('validates amount is positive for transfer', function () {
    $payer = createUserWithBalance(UserRole::USER, 10000);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => -100,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'Invalid deposit amount: -100. Amount must be greater than 0',
        ]);
});

it('validates payer and payee are different for transfer', function () {
    $user = createUserWithBalance(UserRole::USER, 10000);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $user->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'Cannot create deposit where payer and payee are the same user',
        ]);
});

it('validates users exist for transfer', function () {
    $user = $this->createRegularUser();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => 999,
            'payee' => 998,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'User with ID 999 not found',
        ]);
});

it('fails with insufficient balance', function () {
    $payer = createUserWithBalance(UserRole::USER, 1000);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => 'Insufficient balance. Available: 1000, Required: 5000',
        ]);
});

it('fails when payer has invalid role', function () {
    $externalFund = $this->createExternalFund();
    $payee        = $this->createRegularUser();
    $admin        = $this->createAdmin();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $externalFund->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => "User with role 'external_found' cannot perform transfers",
        ]);
});

it('fails when payee has invalid role', function () {
    $payer        = createUserWithBalance(UserRole::USER, 10000);
    $externalFund = $this->createExternalFund();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $payer->id,
            'payee' => $externalFund->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'error' => "User with role 'external_found' cannot recive transfers",
        ]);
});

it('handles transfer with multiple credits', function () {
    $payer        = $this->createRegularUser();
    $payee        = $this->createRegularUser();
    $externalFund = $this->createExternalFund();

    for ($i = 0; $i < 3; $i++) {
        $transaction = Transaction::create([
            'payer_user_id' => $externalFund->id,
            'payee_user_id' => $payer->id,
            'type'          => TransactionType::DEPOSIT,
        ]);

        Credit::create([
            'transaction_id' => $transaction->id,
            'amount'         => 2000,
        ]);
    }

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 5000,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer processed successfully',
        ]);

    $this->assertDatabaseCount('debits', 3);
});
