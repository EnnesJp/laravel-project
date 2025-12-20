<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'document',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN->value;
    }

    public function canDeposit(): bool
    {
        return in_array($this->role, [
            UserRole::ADMIN->value,
            UserRole::EXTERNAL_FOUND->value,
        ]);
    }

    public function canReciveDeposit(): bool
    {
        return in_array($this->role, [
            UserRole::ADMIN->value,
            UserRole::USER->value,
        ]);
    }

    public function canTransfer(): bool
    {
        return in_array($this->role, [
            UserRole::ADMIN->value,
            UserRole::USER->value,
        ]);
    }

    public function canReciveTransfer(): bool
    {
        return in_array($this->role, [
            UserRole::ADMIN->value,
            UserRole::USER->value,
            UserRole::SELLER->value,
        ]);
    }
}
