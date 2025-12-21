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
            'id'            => $this->id,
            'payer_user_id' => $this->payer_user_id,
            'payee_user_id' => $this->payee_user_id,
            'type'          => $this->type->value,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'credits'       => $this->whenLoaded('credits'),
            'debits'        => $this->whenLoaded('debits'),
        ];
    }
}
