<?php

declare(strict_types=1);

use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Models\Credit;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\User\Enums\UserRole;
use Illuminate\Support\Facades\Event;
use Tests\Helpers\MockValidationHelper;
use Tests\Traits\AssertsEvents;
use Tests\Traits\ClearsCache;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, ClearsCache::class, AssertsEvents::class);

beforeEach(function () {
    MockValidationHelper::bindSuccessfulMock();
    $this->clearRedisCache();
    Event::fake();
});

it('allows user to make transfer with sufficient balance', function () {
    $payer = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201);

    expect($response->json('data.payer_user_id'))->toBe($payer->id);
    expect($response->json('data.payee_user_id'))->toBe($payee->id);
    expect($response->json('data.amount'))->toBe(50);

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

    $this->assertTransactionSuccessEvent($payee->id, $payer->id);
});

it('allows admin to make transfer', function () {
    $admin = $this->createUserWithBalance(UserRole::ADMIN, 150);
    $payee = $this->createSeller();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/transfer', [
            'value' => 80.00,
            'payer' => $admin->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer processed successfully',
        ]);

    $this->assertTransactionSuccessEvent($payee->id, $admin->id);
});

it('rejects transfer when external validation fails', function () {
    $reason = 'Suspicious transaction detected';
    MockValidationHelper::bindFailingMock($reason);

    $payer = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'error'   => 'External validation failed: ' . $reason,
        ]);

    $this->assertDatabaseMissing('transactions', [
        'payer_user_id' => $payer->id,
        'payee_user_id' => $payee->id,
        'type'          => 'transfer',
    ]);
});

it('prevents seller from making transfer', function () {
    $seller = $this->createUserWithBalance(UserRole::SELLER, 100);
    $payee  = $this->createRegularUser();

    $response = $this->actingAs($seller)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
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
            'value' => 50.00,
            'payer' => $externalFund->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(403);
});

it('requires authentication for transfer', function () {
    $payer = $this->createRegularUser();
    $payee = $this->createRegularUser();

    $response = $this->postJson('/api/v1/transfer', [
        'value' => 50.00,
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
    $payer = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => -100.0,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('value');
});

it('validates payer and payee are different for transfer', function () {
    $user = $this->createUserWithBalance(UserRole::USER, 100);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $user->id,
            'payee' => $user->id,
        ]);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe('Cannot create deposit where payer and payee are the same user');
});

it('validates users exist for transfer', function () {
    $user = $this->createRegularUser();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $user->id,
            'payee' => 999,
        ]);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe('User with ID 999 not found');
});

it('fails with insufficient balance', function () {
    $payer = $this->createUserWithBalance(UserRole::USER, 10);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe('Insufficient balance. Available: 10,00, Required: 50,00');

    $this->assertTransactionFailedEvent($payee->id, $payer->id);
});

it('fails when payer has invalid role', function () {
    $externalFund = $this->createExternalFund();
    $payee        = $this->createRegularUser();
    $admin        = $this->createAdmin();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $externalFund->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe("User with role 'external_found' cannot perform transfers");

    $this->assertTransactionFailedEvent($payee->id, $externalFund->id);
});

it('fails when payee has invalid role', function () {
    $payer        = $this->createUserWithBalance(UserRole::USER, 100);
    $externalFund = $this->createExternalFund();

    $response = $this->actingAs($payer)
        ->postJson('/api/v1/transfer', [
            'value' => 50.00,
            'payer' => $payer->id,
            'payee' => $externalFund->id,
        ]);

    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe("User with role 'external_found' cannot recive transfers");

    $this->assertTransactionFailedEvent($externalFund->id, $payer->id);
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
            'value' => 50.00,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer processed successfully',
        ]);

    $this->assertDatabaseCount('debits', 3);
    $this->assertTransactionSuccessEvent($payee->id, $payer->id);
});

it('prevents user role from transferring money for others', function () {
    $user1 = $this->createUserWithBalance(UserRole::USER, 100);
    $user2 = $this->createUserWithBalance(UserRole::USER, 50);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($user1)
        ->postJson('/api/v1/transfer', [
            'value' => 30.00,
            'payer' => $user2->id,
            'payee' => $payee->id,
        ]);


    $response->assertStatus(422);

    $json = $response->json();
    expect($json['success'])->toBeFalse()
        ->and($json['error'])->toBe("Users with 'user' role can only transfer their own money");

    $this->assertTransactionFailedEvent($payee->id, $user2->id);
});

it('allows admin to transfer money for others', function () {
    $admin = $this->createAdmin();
    $user  = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createRegularUser();

    $response = $this->actingAs($admin)
        ->postJson('/api/v1/transfer', [
            'value' => 30.0,
            'payer' => $user->id,
            'payee' => $payee->id,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Transfer processed successfully',
        ]);

    $this->assertTransactionSuccessEvent($payee->id, $user->id);
});
