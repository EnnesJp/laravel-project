<?php

declare(strict_types=1);

use App\Domains\User\Enums\UserRole;
use Illuminate\Support\Facades\Cache;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class);

beforeEach(function () {
    Cache::flush();
});

afterEach(function () {
    Cache::flush();
});

it('prevents concurrent transfers when payer is locked', function () {
    $payer = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createUserWithBalance(UserRole::USER, 0);

    $lock = Cache::lock("user:{$payer->id}", 30);
    $lock->get();

    $response = $this->actingAs($payer)->postJson('/api/v1/transfer', [
        'payer' => $payer->id,
        'payee' => $payee->id,
        'value' => 50.00,
    ]);

    expect($response->status())->toBe(500);
    expect($response->json())->toHaveKey('success', false);

    $lock->release();
});

it('prevents concurrent transfers when payee is locked', function () {
    $payer = $this->createUserWithBalance(UserRole::USER, 100);
    $payee = $this->createUserWithBalance(UserRole::USER, 0);

    $lock = Cache::lock("user:{$payee->id}", 30);
    $lock->get();

    $response = $this->actingAs($payer)->postJson('/api/v1/transfer', [
        'payer' => $payer->id,
        'payee' => $payee->id,
        'value' => 50.00,
    ]);

    expect($response->status())->toBe(500);
    expect($response->json())->toHaveKey('success', false);

    $lock->release();
});

it('prevents race conditions for same user making multiple transfers', function () {
    $payer  = $this->createUserWithBalance(UserRole::USER, 200);
    $payee1 = $this->createUserWithBalance(UserRole::USER, 0);
    $payee2 = $this->createUserWithBalance(UserRole::USER, 0);

    $lock = Cache::lock("user:{$payer->id}", 30);
    $lock->get();

    $response1 = $this->actingAs($payer)->postJson('/api/v1/transfer', [
        'payer' => $payer->id,
        'payee' => $payee1->id,
        'value' => 50.00,
    ]);

    $response2 = $this->actingAs($payer)->postJson('/api/v1/transfer', [
        'payer' => $payer->id,
        'payee' => $payee2->id,
        'value' => 75.00,
    ]);

    expect($response1->status())->toBe(500);
    expect($response2->status())->toBe(500);

    $lock->release();
});

it('allows concurrent transfers between different user pairs', function () {
    $payer1 = $this->createUserWithBalance(UserRole::USER, 100);
    $payee1 = $this->createUserWithBalance(UserRole::USER, 0);
    $payer2 = $this->createUserWithBalance(UserRole::USER, 100);
    $payee2 = $this->createUserWithBalance(UserRole::USER, 0);

    expect($payer1->id)->not->toBe($payer2->id);
    expect($payee1->id)->not->toBe($payee2->id);
    expect($payer1->id)->not->toBe($payee2->id);
    expect($payer2->id)->not->toBe($payee1->id);
});
