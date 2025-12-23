<?php

declare(strict_types=1);

namespace App\Domains\Transaction\DTOs;

use App\Domains\Transaction\Events\TransactionSuccess;
use App\DTOs\NotificationDTO;

class TransactionSuccessNotificationDTO extends NotificationDTO
{
    public function __construct(
        public readonly int $payeeUserId,
        public readonly ?int $payerUserId = null,
        ?string $timestamp = null
    ) {
        parent::__construct(
            type: 'transaction_success',
            timestamp: $timestamp ?? self::now()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type'          => $this->type,
            'payee_user_id' => $this->payeeUserId,
            'payer_user_id' => $this->payerUserId,
            'timestamp'     => $this->timestamp,
        ];
    }

    public static function fromEvent(TransactionSuccess $event): self
    {
        return new self(
            payeeUserId: $event->payeeUserId,
            payerUserId: $event->payerUserId
        );
    }
}
