<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Services\Contracts\DepositServiceInterface;
use Illuminate\Http\JsonResponse;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositServiceInterface $depositService
    ) {
    }

    public function store(
        DepositRequest $request
    ): JsonResponse {
        $transaction = $this->depositService->execute(
            walletId: $request->user()->wallet->id,
            amount: $request->validated('amount')
        );

        return response()->json([
            'message' => 'Deposit completed successfully.',
            'data' => $transaction,
        ], 201);
    }
}