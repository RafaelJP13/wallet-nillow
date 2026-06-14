<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReverseTransactionRequest;
use App\Models\Transaction;
use App\Services\Contracts\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;

class ReversalController extends Controller
{
    public function __construct(
        private readonly TransactionServiceInterface $transactionService
    ) {
    }

    public function store(
        ReverseTransactionRequest $request,
        Transaction $transaction
    ): JsonResponse {
        $transaction = $this->transactionService->reverse(
            transactionId: $transaction->id,
            reversedBy: $request->user()->id,
            reason: $request->validated('reason')
        );

        return response()->json([
            'message' => 'Transaction reversed successfully.',
            'data' => $transaction,
        ]);
    }
}