<?php

namespace App\Livewire\Wallet;

use App\Services\Contracts\TransactionServiceInterface;
use DomainException;
use Throwable;
use Livewire\Component;
use App\Models\Wallet;

class TransferForm extends Component
{
    public string $toWalletId = '';
    public string $amount = '';
    public ?Wallet $toWallet = null;
    public bool $showPreview = false;

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
                'Não foi possivel concluir a transferência.'
            );

            return;
        }

        $this->reset(['toWalletId', 'amount']);
        $this->dispatch('wallet-updated');
        session()->flash('wallet-status', 'Transferência enviada com sucesso.');
    }

    public function render()
    {
        return view('livewire.wallet.transfer-form');
    }

    public function previewTransfer(): void
    {
        $validated = $this->validate([
            'toWalletId' => ['required', 'integer', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ((int) $validated['toWalletId'] === auth()->user()->wallet->id) {
            $this->addError('toWalletId', 'Escolha uma carteira diferente da sua.');
            return;
        }

        $this->toWallet = Wallet::with('user')->find($validated['toWalletId']);
        $this->showPreview = true;
    }


    public function confirmTransfer(TransactionServiceInterface $transactionService): void
    {
        if (!$this->showPreview || !$this->toWallet) {
            $this->addError('toWalletId', 'Confirmação inválida.');
            return;
        }

        try {
            $transactionService->transfer(
                fromWalletId: auth()->user()->wallet->id,
                toWalletId: (int) $this->toWalletId,
                amount: (float) $this->amount
            );
        } catch (DomainException $exception) {
            $this->addError('amount', $exception->getMessage());
            return;
        } catch (Throwable) {
            $this->addError('amount', 'Não foi possivel concluir a transferência.');
            return;
        }

        // limpa estado após sucesso
        $this->reset([
            'toWalletId',
            'amount',
            'toWallet',
            'showPreview',
        ]);

        $this->dispatch('wallet-updated');

        session()->flash(
            'wallet-status',
            'Transferência enviada com sucesso.'
        );
    }

    
}
