<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemainingCredit extends Model
{
    protected $fillable = [
        'credit_id',
        'user_id',
        'remaining',
    ];

    protected $casts = [
        'remaining' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
}
