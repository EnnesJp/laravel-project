<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Exceptions\InvalidDepositException;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domains\Transaction\Services\Validation\DepositValidationService;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private readonly DepositValidationService $validationService,
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreditService $creditService,
        private readonly DebitService $debitService,
        private readonly BalanceCacheService $cacheService
    ) {
    }

    /**
     * @throws InvalidDepositException
     */
    public function deposit(DepositDTO $dto): Transaction
    {
        $this->validationService->validateDeposit($dto);

        try {
            return DB::transaction(function () use ($dto) {
                $transactionDTO = new CreateTransactionDTO(
                    payerUserId: $dto->payer,
                    payeeUserId: $dto->payee,
                    type: TransactionType::DEPOSIT
                );
                $transaction = $this->repository->create($transactionDTO);

                $this->creditService->createCredit($transaction->id, $dto->amount);
                $this->debitService->createFundDebit($transaction->id, $dto->amount);

                $this->cacheService->updateUserBalance($dto->payee, $dto->amount);

                return $this->repository->findByIdWithRelations(
                    $transaction->id,
                    ['credits', 'debits']
                );
            });
        } catch (\Exception $e) {
            event(new TransactionFailed($dto->payee));
            throw $e;
        }
    }
}
