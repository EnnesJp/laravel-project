<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\TransferDTO;
use App\Exceptions\InvalidDepositException;
use App\Exceptions\InvalidTransferException;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Responses\JsonResponse;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse as BaseJsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function deposit(DepositRequest $request): BaseJsonResponse
    {
        try {
            $dto = DepositDTO::fromRequest($request);

            $transaction = $this->transactionService->deposit($dto);

            return JsonResponse::created(
                new TransactionResource($transaction),
                'Deposit processed successfully'
            );

        } catch (InvalidDepositException $e) {
            return JsonResponse::error(
                $e->getMessage(),
                ['error' => $e->getMessage()],
                $e->getCode()
            );
        } catch (\Exception $e) {
            return JsonResponse::error(
                'Failed to process deposit',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    public function transfer(TransferRequest $request): BaseJsonResponse
    {
        try {
            $dto           = TransferDTO::fromRequest($request);
            $currentUserId = $request->user()->id;

            $transaction = $this->transactionService->transfer($dto, $currentUserId);

            return JsonResponse::created(
                new TransactionResource($transaction),
                'Transfer processed successfully'
            );

        } catch (InvalidTransferException $e) {
            return JsonResponse::error(
                $e->getMessage(),
                ['error' => $e->getMessage()],
                $e->getCode()
            );
        } catch (\Exception $e) {
            return JsonResponse::error(
                'Failed to process transfer',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
