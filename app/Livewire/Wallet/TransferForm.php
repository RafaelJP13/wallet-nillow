<?php

namespace App\Livewire\Wallet;

use App\Services\Contracts\TransactionServiceInterface;
use DomainException;
use Throwable;
use Livewire\Component;

class TransferForm extends Component
{
    public string $toWalletId = '';

    public string $amount = '';

    public function transfer(
        TransactionServiceInterface $transactionService
    ): void {
        $validated = $this->validate([
            'toWalletId' => ['required', 'integer', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ], [
            'toWalletId.exists' => 'Informe uma carteira existente.',
        ]);

        if ((int) $validated['toWalletId'] === auth()->user()->wallet->id) {
            $this->addError(
                'toWalletId',
                'Escolha uma carteira diferente da sua.'
            );

            return;
        }

        try {
            $transactionService->transfer(
                fromWalletId: auth()->user()->wallet->id,
                toWalletId: (int) $validated['toWalletId'],
                amount: (float) $validated['amount']
            );
        } catch (DomainException $exception) {
            $this->addError('amount', $exception->getMessage());

            return;
        } catch (Throwable) {
            $this->addError(
                'amount',
                'Nao foi possivel concluir a transferencia.'
            );

            return;
        }

        $this->reset(['toWalletId', 'amount']);
        $this->dispatch('wallet-updated');
        session()->flash('wallet-status', 'Transferencia enviada com sucesso.');
    }

    public function render()
    {
        return view('livewire.wallet.transfer-form');
    }
}
