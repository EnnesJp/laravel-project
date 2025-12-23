<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Domains\Transaction\Events\TransactionSuccess;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTransactionSuccessNotification
{
    public function handle(TransactionSuccess $event): void
    {
        $notificationUrl = config('services.notification.url');

        if (empty($notificationUrl)) {
            Log::warning('Notification URL not configured');
            return;
        }

        try {
            /** @var Response $response */
            $response = Http::timeout(10)->post($notificationUrl, [
                'type'          => 'transaction_success',
                'payee_user_id' => $event->payeeUserId,
                'payer_user_id' => $event->payerUserId,
                'timestamp'     => now()->toISOString(),
            ]);

            $statusCode = $response->status();
            if ($statusCode >= 400) {
                Log::error('Failed to send transaction success notification', [
                    'status'        => $statusCode,
                    'response'      => $response->body(),
                    'payee_user_id' => $event->payeeUserId,
                    'payer_user_id' => $event->payerUserId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending transaction success notification', [
                'message'       => $e->getMessage(),
                'payee_user_id' => $event->payeeUserId,
                'payer_user_id' => $event->payerUserId,
            ]);
        }
    }
}
