<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateCreditDTO;
use App\DTOs\Transaction\CreateDebitDTO;
use App\DTOs\Transaction\CreateFundDebitDTO;
use App\DTOs\Transaction\CreateTransactionDTO;
use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\TransferDTO;
use App\Enums\TransactionType;
use App\Exceptions\InvalidDepositException;
use App\Exceptions\InvalidTransferException;
use App\Models\Transaction;
use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\DebitRepositoryInterface;
use App\Repositories\Contracts\FundDebitRepositoryInterface;
use App\Repositories\Contracts\RemainingCreditRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreditRepositoryInterface $creditRepository,
        private readonly RemainingCreditRepositoryInterface  $remainingCreditRepository,
        private readonly DebitRepositoryInterface $debitRepository,
        private readonly FundDebitRepositoryInterface $fundDebitRepository
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
            $transaction = $this->repository->create($transactionDTO);

            $creditDTO = new CreateCreditDTO(
                transaction_id: $transaction->id,
                amount: $dto->amount
            );
            $this->creditRepository->create($creditDTO);

            $debitDTO = new CreateFundDebitDTO(
                transaction_id: $transaction->id,
                amount: $dto->amount
            );
            $this->fundDebitRepository->create($debitDTO);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['payer', 'payee', 'credits', 'debits']
            );
        });
    }

    /**
     * @throws InvalidTransferException
     */
    public function transfer(TransferDTO $dto): Transaction
    {
        $this->validateTransfer($dto);

        return DB::transaction(function () use ($dto) {
            $transactionDTO = new CreateTransactionDTO(
                payer_user_id: $dto->payer,
                payee_user_id: $dto->payee,
                type: TransactionType::TRANSFER
            );
            $transaction = $this->repository->create($transactionDTO);

            $creditDTO = new CreateCreditDTO(
                transaction_id: $transaction->id,
                amount: $dto->amount
            );
            $this->creditRepository->create($creditDTO);

            $this->handlePayerDebits($dto, $transaction);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['credits', 'debits']
            );
        });
    }

    private function handlePayerDebits(TransferDTO $dto, Transaction $transaction): void
    {
        $availableCredits = $this->remainingCreditRepository->getRemainingCreditsByUserId($dto->payer);
        $availableBalance = $availableCredits->sum('remaining');

        if ($availableBalance < $dto->amount) {
            throw InvalidTransferException::insufficientBalance($availableBalance, $dto->amount);
        }

        $debitsToCreate  = collect();
        $remainingAmount = $dto->amount;

        foreach ($availableCredits as $credit) {
            if ($remainingAmount <= 0) {
                break;
            }

            $debitAmount = min($remainingAmount, $credit->remaining);

            $debitDTO = new CreateDebitDTO(
                transaction_id: $transaction->id,
                credit_id: $credit->credit_id,
                amount: $debitAmount
            );

            $debitsToCreate->push($debitDTO);
            $remainingAmount -= $debitAmount;
        }

        $this->debitRepository->bulkInsert($debitsToCreate);
    }

    /**
     * @throws InvalidTransferException
     */
    private function validateTransfer(TransferDTO $dto): void
    {
        if ($dto->amount <= 0) {
            throw InvalidTransferException::invalidAmount($dto->amount);
        }

        if ($dto->payer === $dto->payee) {
            throw InvalidTransferException::sameUser();
        }

        $payer = $this->userService->findById($dto->payer);
        $payee = $this->userService->findById($dto->payee);

        if (!$payer) {
            throw InvalidTransferException::userNotFound($dto->payer);
        }

        if (!$payee) {
            throw InvalidTransferException::userNotFound($dto->payee);
        }

        if (!$payer->canTransfer()) {
            throw InvalidTransferException::invalidPayerRole($payer->role);
        }

        if (!$payee->canReciveTransfer()) {
            throw InvalidTransferException::invalidPayeeRole($payee->role);
        }
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

        $payer = $this->userService->findById($dto->payer);
        $payee = $this->userService->findById($dto->payee);

        if (!$payer) {
            throw InvalidDepositException::userNotFound($dto->payer);
        }

        if (!$payee) {
            throw InvalidDepositException::userNotFound($dto->payee);
        }

        if (!$payer->canDeposit()) {
            throw InvalidDepositException::invalidPayerRole($payer->role);
        }

        if (!$payee->canReciveDeposit()) {
            throw InvalidDepositException::invalidPayeeRole($payee->role);
        }
    }
}
