<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'payer_user_id' => (int) $this->payer_user_id,
            'payee_user_id' => (int) $this->payee_user_id,
            'amount'        => (float) ($this->credit->amount / 100),
        ];
    }
}
