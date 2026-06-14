<?php

namespace App\Livewire\Wallet;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Services\Contracts\TransactionServiceInterface;
use DomainException;
use Livewire\Attributes\On;
use Livewire\Component;

class TransactionHistory extends Component
{
    public ?int $selectedTransactionId = null;

    public ?int $reversingTransactionId = null;

    public string $reason = '';

    #[On('wallet-updated')]
    public function refreshHistory(): void
    {
    }

    public function startReversal(int $transactionId): void
    {
        $this->resetErrorBag();
        $this->reversingTransactionId = $transactionId;
        $this->selectedTransactionId = $transactionId;
        $this->reason = '';
    }

    public function toggleDetails(int $transactionId): void
    {
        $transactionId = (int) $transactionId;

        $this->selectedTransactionId = $this->selectedTransactionId === $transactionId
            ? null
            : $transactionId;
    }

    public function cancelReversal(): void
    {
        $this->reset(['reversingTransactionId', 'reason']);
    }

    public function reverse(
        TransactionServiceInterface $transactionService
    ): void {
        $validated = $this->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $transaction = $this->transactionsQuery()
            ->whereKey($this->reversingTransactionId)
            ->firstOrFail();

        try {
            $transactionService->reverse(
                transactionId: $transaction->id,
                reversedBy: auth()->id(),
                reason: $validated['reason']
            );
        } catch (DomainException $exception) {
            $this->addError('reason', $exception->getMessage());

            return;
        }

        $this->reset(['reversingTransactionId', 'reason']);
        $this->dispatch('wallet-updated');
        session()->flash('wallet-status', 'Transacao revertida com sucesso.');
    }

    public function render()
    {
        return view('livewire.wallet.transaction-history', [
            'transactions' => $this->transactionsQuery()
                ->with([
                    'detail',
                    'fromWallet.user',
                    'toWallet.user',
                    'reversal.user',
                ])
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    private function transactionsQuery()
    {
        $walletId = auth()->user()->wallet->id;

        return Transaction::query()
            ->where(function ($query) use ($walletId) {
                $query
                    ->where('from_wallet_id', $walletId)
                    ->orWhere('to_wallet_id', $walletId);
            });
    }

    public function canReverse(Transaction $transaction): bool
    {
        return $transaction->status === TransactionStatus::COMPLETED
            && $transaction->reversal === null
            && $transaction->to_wallet_id === auth()->user()->wallet->id;
    }

    public function canShowBalanceDetails(Transaction $transaction): bool
    {
        $walletId = auth()->user()->wallet->id;

        return $transaction->from_wallet_id === $walletId
            || (
                $transaction->from_wallet_id === null
                && $transaction->to_wallet_id === $walletId
            );
    }
}
