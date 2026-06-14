<?php

namespace App\Livewire\Wallet;

use App\Models\Wallet;
use Livewire\Attributes\On;
use Livewire\Component;

class Summary extends Component
{
    public Wallet $wallet;

    public function mount(): void
    {
        $this->loadWallet();
    }

    #[On('wallet-updated')]
    public function loadWallet(): void
    {
        $this->wallet = auth()
            ->user()
            ->wallet()
            ->firstOrCreate([
                'user_id' => auth()->id(),
            ], [
                'balance' => 0,
            ])
            ->refresh();
    }

    public function render()
    {
        return view('livewire.wallet.summary');
    }
}
