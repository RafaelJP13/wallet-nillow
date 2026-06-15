<section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-950">Transferir</h3>

    <form wire:submit="previewTransfer" class="mt-5 space-y-4">
        <div>
            <x-input-label for="to-wallet-id" value="Carteira de destino" />
            <x-text-input
                id="to-wallet-id"
                wire:model="toWalletId"
                type="number"
                min="1"
                class="mt-1 block w-full"
                placeholder="ID da carteira"
            />
            <x-input-error :messages="$errors->get('toWalletId')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="transfer-amount" value="Valor" />
            <x-text-input
                id="transfer-amount"
                wire:model="amount"
                type="number"
                step="0.01"
                min="0.01"
                class="mt-1 block w-full"
                placeholder="0,00"
            />
            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>

        <x-primary-button>
            Transferir
        </x-primary-button>
    </form>
    @if($showPreview && $toWallet)
    <div class="mt-6 rounded-lg border bg-gray-50 p-4">
        <h4 class="text-lg font-semibold">Confirmar transferência</h4>

        <p class="mt-2 text-sm">
            <strong>Destinatário:</strong> {{ $toWallet->user->name }}
        </p>

        <p class="text-sm">
            <strong>Carteira ID:</strong> {{ $toWallet->id }}
        </p>

        <p class="text-sm">
            <strong>Valor:</strong> R$ {{ number_format($amount, 2, ',', '.') }}
        </p>

       <div class="mt-4 flex gap-2">
            <x-primary-button wire:click="confirmTransfer" type="button">
                Confirmar
            </x-primary-button>

            <button
                type="button"
                wire:click="$set('showPreview', false)"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50"
            >
                Cancelar
            </button>
        </div>
        </div>
    </div>
@endif
</section>
