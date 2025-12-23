<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Adapters;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Exceptions\ExternalValidationException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpValidationAdapter implements ValidationAdapterInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeoutSeconds = 10
    ) {
    }

    /**
     * @throws ExternalValidationException
     */
    public function validateTransfer(TransferDTO $dto): bool
    {
        try {
            /** @var Response $response */
            $response = Http::timeout($this->timeoutSeconds)
                ->get("{$this->baseUrl}/authorize", [
                    'payer_id' => $dto->payer,
                    'payee_id' => $dto->payee,
                    'amount'   => $dto->amount,
                ]);

            $statusCode = $response->status();
            if ($statusCode >= 400) {
                Log::warning('External validation service returned error', [
                    'status' => $statusCode,
                    'body'   => $response->body(),
                ]);

                throw ExternalValidationException::serviceUnavailable();
            }

            $data = $response->json();

            if (!is_array($data) || !isset($data['data'])) {
                throw ExternalValidationException::invalidResponse();
            }

            if (!$data['data']['authorization']) {
                $reason = $data['reason'] ?? 'Unknown reason';
                throw ExternalValidationException::validationFailed($reason);
            }

            return true;

        } catch (\Exception $e) {
            if ($e instanceof ExternalValidationException) {
                throw $e;
            }

            Log::error('External validation service error', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            throw ExternalValidationException::serviceUnavailable();
        }
    }
}
