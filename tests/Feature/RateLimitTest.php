<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('api');
});

it('allows requests within rate limit', function () {
    for ($i = 0; $i < 5; $i++) {
        $response = $this->postJson('/api/v1/users', [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'document' => '12345678901',
            'password' => 'Password123!',
            'role'     => 'admin',
        ]);

        expect($response->getStatusCode())->not->toBe(429);
    }
});

it('blocks requests over rate limit', function () {
    $responses = [];

    for ($i = 0; $i < 65; $i++) {
        $response = $this->postJson('/api/v1/users', [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'document' => '12345678901',
            'password' => 'Password123!',
            'role'     => 'admin',
        ]);

        $responses[] = $response->getStatusCode();
    }

    expect($responses)->toContain(429);
});
