<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateCreditDTO;
use App\DTOs\Transaction\CreateFundDebitDTO;
use App\DTOs\Transaction\CreateTransactionDTO;
use App\DTOs\Transaction\DepositDTO;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Exceptions\InvalidDepositException;
use App\Models\Transaction;
use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\FundDebitRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly CreditRepositoryInterface $creditRepository,
        private readonly FundDebitRepositoryInterface $debitRepository
    ) {
    }

    /**
     * @throws InvalidDepositException
     */
    public function deposit(DepositDTO $dto): Transaction
    {
        $this->validateDeposit($dto);

        return DB::transaction(function () use ($dto) {
            $transactionDTO = new CreateTransactionDTO(
                payer_user_id: $dto->payer,
                payee_user_id: $dto->payee,
                type: TransactionType::DEPOSIT
            );
            $transaction = $this->transactionRepository->create($transactionDTO);

            $creditDTO = new CreateCreditDTO(
                entry_id: $transaction->id,
                amount: $dto->amount
            );
            $this->creditRepository->create($creditDTO);

            $debitDTO = new CreateFundDebitDTO(
                entry_id: $transaction->id,
                amount: $dto->amount
            );
            $this->debitRepository->create($debitDTO);

            return $this->transactionRepository->findByIdWithRelations(
                $transaction->id,
                ['payer', 'payee', 'credits', 'debits']
            );
        });
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDeposit(DepositDTO $dto): void
    {
        if ($dto->amount <= 0) {
            throw InvalidDepositException::invalidAmount($dto->amount);
        }

        if ($dto->payer === $dto->payee) {
            throw InvalidDepositException::sameUser();
        }

        $payer = $this->userRepository->find($dto->payer);
        $payee = $this->userRepository->find($dto->payee);

        if (!$payer) {
            throw InvalidDepositException::userNotFound($dto->payer);
        }

        if (!$payee) {
            throw InvalidDepositException::userNotFound($dto->payee);
        }

        if ($payer->role !== UserRole::EXTERNAL_FOUND) {
            throw InvalidDepositException::invalidPayerRole($payer->role->value);
        }

        if ($payee->role === UserRole::EXTERNAL_FOUND) {
            throw InvalidDepositException::invalidPayeeRole($payee->role->value);
        }
    }
}
