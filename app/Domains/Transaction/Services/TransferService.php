<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Exceptions\ExternalValidationException;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domains\Transaction\Services\Validation\TransferValidationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private readonly TransferValidationService $validationService,
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreditService $creditService,
        private readonly DebitService $debitService,
        private readonly ValidationAdapterInterface $externalValidation
    ) {
    }

    /**
     * @throws InvalidTransferException
     * @throws ExternalValidationException
     */
    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        $this->validationService->validateTransferData($dto, $currentUserId);

        return $this->lockUsersForTransfer($dto->payer, $dto->payee, function () use ($dto) {
            return DB::transaction(function () use ($dto) {
                $transactionDTO = new CreateTransactionDTO(
                    payerUserId: $dto->payer,
                    payeeUserId: $dto->payee,
                    type: TransactionType::TRANSFER
                );
                $transaction = $this->repository->create($transactionDTO);

                $this->creditService->createCredit($transaction->id, $dto->amount);

                $this->debitService->createDebits(
                    $dto->payer,
                    $dto->amount,
                    $transaction->id
                );

                $this->externalValidation->validateTransfer($dto);

                return $this->repository->findByIdWithRelations(
                    $transaction->id,
                    ['credit']
                );
            });
        });
    }

    private function lockUsersForTransfer(int $payerId, int $payeeId, callable $callback): mixed
    {
        $userIds = [$payerId, $payeeId];
        sort($userIds);

        $firstUserId  = $userIds[0];
        $secondUserId = $userIds[1];

        $firstLock = Cache::lock("user:{$firstUserId}", config('app.redis_lock_timeout'));

        return $firstLock->block(config('app.redis_lock_max_retries'), function () use ($secondUserId, $callback) {
            $secondLock = Cache::lock("user:{$secondUserId}", config('app.redis_lock_timeout'));

            return $secondLock->block(config('app.redis_lock_max_retries'), function () use ($callback) {
                return $callback();
            });
        });
    }
}
