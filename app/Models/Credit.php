<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    protected $fillable = [
        'entry_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'entry_id');
    }

    public function debits(): HasMany
    {
        return $this->hasMany(Debit::class, 'credit_id');
    }
}
