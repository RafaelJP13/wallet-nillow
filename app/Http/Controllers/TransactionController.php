<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Contracts\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionServiceInterface $transactionService,
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $transactions = $this->transactionRepository
            ->paginateForUser(
                userId: $request->user()->id
            );

        return response()->json($transactions);
    }

    public function store(
        TransactionRequest $request
    ): JsonResponse {
        $transaction = $this->transactionService->transfer(
            fromWalletId: $request->user()->wallet->id,
            toWalletId: $request->validated('to_wallet_id'),
            amount: $request->validated('amount')
        );

        return response()->json([
            'message' => 'Transferência feita com sucesso.',
            'data' => $transaction,
        ], 201);
    }

    public function show(
        Transaction $transaction
    ): JsonResponse {
        $transaction->load([
            'detail',
            'reversal',
            'fromWallet.user',
            'toWallet.user',
        ]);

        return response()->json([
            'data' => $transaction,
        ]);
    }
}