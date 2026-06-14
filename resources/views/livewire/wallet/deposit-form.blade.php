<section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-950">Depositar</h3>

    <form wire:submit="deposit" class="mt-5 space-y-4">
        <div>
            <x-input-label for="deposit-amount" value="Valor" />
            <x-text-input
                id="deposit-amount"
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
            Depositar
        </x-primary-button>
    </form>
</section>
