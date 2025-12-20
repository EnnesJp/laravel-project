<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'payer_user_id',
        'payee_user_id',
        'type',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_user_id');
    }

    public function credits(): HasOne
    {
        return $this->hasOne(Credit::class, 'transaction_id');
    }

    public function debits(): HasMany
    {
        return $this->hasMany(Debit::class, 'transaction_id');
    }

    public function fundDebit(): HasOne
    {
        return $this->hasOne(FundDebit::class, 'transaction_id');
    }
}
