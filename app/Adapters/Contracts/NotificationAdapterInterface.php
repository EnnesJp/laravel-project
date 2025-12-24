<?php

declare(strict_types=1);

namespace App\Adapters\Contracts;

use App\DTOs\NotificationDTO;

interface NotificationAdapterInterface
{
    public function send(NotificationDTO $notification): void;
}
