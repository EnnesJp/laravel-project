<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\Transaction\DepositDTO;
use App\Http\Requests\DepositRequest;
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

        } catch (\Exception $e) {
            return JsonResponse::error(
                'Failed to process deposit',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
