<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if (session('wallet-status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('wallet-status') }}
            </div>
        @endif

        <livewire:wallet.summary />

        <div class="grid gap-6 lg:grid-cols-2">
            <livewire:wallet.deposit-form />
            <livewire:wallet.transfer-form />
        </div>

        <livewire:wallet.transaction-history />
    </div>
</div>
