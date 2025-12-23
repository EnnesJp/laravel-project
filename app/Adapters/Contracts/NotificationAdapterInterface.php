<?php

declare(strict_types=1);

namespace App\Adapters\Contracts;

use App\DTOs\NotificationDTO;

/**
 * Generic notification adapter interface.
 *
 * This adapter is agnostic to the type of notification being sent.
 * The specific notification logic (payload structure, type, etc.)
 * should be handled by the calling code (listeners, services, etc.).
 */
interface NotificationAdapterInterface
{
    public function send(NotificationDTO $notification): void;
}
