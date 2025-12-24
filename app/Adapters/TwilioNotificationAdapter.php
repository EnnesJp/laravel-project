<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\DTOs\NotificationDTO;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TwilioNotificationAdapter implements NotificationAdapterInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey = '',
        private readonly int $timeoutSeconds = 10
    ) {
    }

    public function send(NotificationDTO $notification): void
    {
        if (empty($this->baseUrl)) {
            Log::warning('SMS notification URL not configured');
            return;
        }

        $payload = $this->buildSmsPayload($notification);

        try {
            $headers = [];
            if (!empty($this->apiKey)) {
                $headers['Authorization'] = "Bearer {$this->apiKey}";
            }

            /** @var Response $response */
            $response = Http::timeout($this->timeoutSeconds)
                ->withHeaders($headers)
                ->post("{$this->baseUrl}/notify", $payload);

            $statusCode = $response->status();
            if ($statusCode >= 400) {
                $errorMessage = "HTTP {$statusCode}: {$response->body()}";
                Log::error('Failed to send SMS notification', [
                    'status'   => $statusCode,
                    'response' => $response->body(),
                    'type'     => $notification->type,
                    'payload'  => $payload,
                ]);

                throw new RuntimeException("SMS notification failed: {$errorMessage}");
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending SMS notification', [
                'message' => $e->getMessage(),
                'type'    => $notification->type,
                'payload' => $payload,
            ]);

            throw $e;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSmsPayload(NotificationDTO $notification): array
    {
        $basePayload = $notification->toArray();
        
        $basePayload['channel'] = 'sms';
        
        return $basePayload;
    }
}