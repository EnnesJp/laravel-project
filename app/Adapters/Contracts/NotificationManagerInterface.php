<?php

declare(strict_types=1);

namespace App\Adapters\Contracts;

use App\DTOs\NotificationDTO;

interface NotificationManagerInterface
{
    public function sendEmail(NotificationDTO $notification): void;

    public function sendSms(NotificationDTO $notification): void;
}
