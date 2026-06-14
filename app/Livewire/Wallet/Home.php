<?php

namespace App\Livewire\Wallet;

use Illuminate\Support\HtmlString;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        if (auth()->check()) {
            return view('livewire.wallet.home')
                ->layout('layouts.app', [
                    'header' => new HtmlString(
                        view('livewire.wallet.partials.header')->render()
                    ),
                ]);
        }

        return view('livewire.wallet.guest-home')
            ->layout('layouts.guest');
    }
}
