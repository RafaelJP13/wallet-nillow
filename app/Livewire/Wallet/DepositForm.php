<?php

namespace App\Livewire\Wallet;

use App\Services\Contracts\DepositServiceInterface;
use Throwable;
use Livewire\Component;

class DepositForm extends Component
{
    public string $amount = '';

    public function deposit(
        DepositServiceInterface $depositService
    ): void {
        $validated = $this->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $depositService->execute(
                walletId: auth()->user()->wallet->id,
                amount: (float) $validated['amount']
            );
        } catch (Throwable) {
            $this->addError(
                'amount',
                'Nao foi possivel concluir o deposito.'
            );

            return;
        }

        $this->reset('amount');
        $this->dispatch('wallet-updated');
        session()->flash('wallet-status', 'Deposito realizado com sucesso.');
    }

    public function render()
    {
        return view('livewire.wallet.deposit-form');
    }
}
