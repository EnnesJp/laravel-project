<?php

declare(strict_types=1);

namespace App\Domains\User\Models;

use App\Domains\User\Enums\UserRole;
use App\ValueObjects\Document\Base\Document;
use App\ValueObjects\Document\Factory\DocumentFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
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

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getDocumentObject(): Document
    {
        return DocumentFactory::create($this->document);
    }

    public function getFormattedDocument(): string
    {
        return $this->getDocumentObject()->getFormatted();
    }

    public function getDocumentType(): string
    {
        return DocumentFactory::getDocumentType($this->document);
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
