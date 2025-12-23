<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\DTOs\NotificationDTO;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpNotificationAdapter implements NotificationAdapterInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeoutSeconds = 10
    ) {
    }

    public function send(NotificationDTO $notification): void
    {
        if (empty($this->baseUrl)) {
            Log::warning('Notification URL not configured');
            return;
        }

        $payload = $notification->toArray();

        try {
            /** @var Response $response */
            $response = Http::timeout($this->timeoutSeconds)
                ->post("{$this->baseUrl}/notify", $payload);

            $statusCode = $response->status();
            if ($statusCode >= 400) {
                Log::error('Failed to send notification', [
                    'status'   => $statusCode,
                    'response' => $response->body(),
                    'type'     => $notification->type,
                    'payload'  => $payload,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending notification', [
                'message' => $e->getMessage(),
                'type'    => $notification->type,
                'payload' => $payload,
            ]);
        }
    }
}
