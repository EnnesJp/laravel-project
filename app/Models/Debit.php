<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debit extends Model
{
    protected $fillable = [
        'entry_id',
        'credit_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'entry_id');
    }

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class, 'credit_id');
    }
}
