<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-5">
        <h3 class="text-lg font-semibold text-gray-950">Historico</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3">Valor</th>
                    <th class="px-6 py-3">Origem</th>
                    <th class="px-6 py-3">Destino</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($transactions as $transaction)
                    <tr wire:key="transaction-row-{{ $transaction->id }}">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ ucfirst($transaction->type->value) }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            R$ {{ number_format((float) $transaction->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $transaction->fromWallet?->user?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $transaction->toWallet?->user?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                {{ $transaction->status->value }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    wire:key="transaction-details-button-{{ $transaction->id }}"
                                    wire:click.prevent="toggleDetails({{ $transaction->id }})"
                                    class="text-sm font-semibold text-gray-700 hover:text-gray-950"
                                >
                                    Detalhes
                                </button>

                                @if ($this->canReverse($transaction))
                                    <button
                                        type="button"
                                        wire:click="startReversal({{ $transaction->id }})"
                                        class="text-sm font-semibold text-red-700 hover:text-red-900"
                                    >
                                        Reverter
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if ($selectedTransactionId === $transaction->id)
                        <tr wire:key="transaction-details-row-{{ $transaction->id }}" class="bg-gray-50">
                            <td colspan="6" class="px-6 py-5">
                                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <div class="rounded-md border border-gray-200 bg-white p-4">
                                        <div class="text-xs font-medium uppercase text-gray-500">Transacao</div>
                                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                                            <p><span class="font-semibold text-gray-900">ID:</span> #{{ $transaction->id }}</p>
                                            <p><span class="font-semibold text-gray-900">Tipo:</span> {{ ucfirst($transaction->type->value) }}</p>
                                            <p><span class="font-semibold text-gray-900">Descricao:</span> {{ $transaction->description ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="rounded-md border border-gray-200 bg-white p-4">
                                        <div class="text-xs font-medium uppercase text-gray-500">Carteiras</div>
                                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                                            <p><span class="font-semibold text-gray-900">Origem:</span> {{ $transaction->fromWallet?->user?->name ?? '-' }}</p>
                                            <p><span class="font-semibold text-gray-900">Destino:</span> {{ $transaction->toWallet?->user?->name ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="rounded-md border border-gray-200 bg-white p-4">
                                        <div class="text-xs font-medium uppercase text-gray-500">Saldo da sua carteira</div>
                                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                                            @if ($this->canShowBalanceDetails($transaction) && $transaction->detail)
                                                <p>
                                                    <span class="font-semibold text-gray-900">Antes:</span>
                                                    R$ {{ number_format((float) $transaction->detail->balance_before, 2, ',', '.') }}
                                                </p>
                                                <p>
                                                    <span class="font-semibold text-gray-900">Depois:</span>
                                                    R$ {{ number_format((float) $transaction->detail->balance_after, 2, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="text-gray-500">
                                                    Saldo de outra carteira oculto.
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="rounded-md border border-gray-200 bg-white p-4">
                                        <div class="text-xs font-medium uppercase text-gray-500">Reversao</div>
                                        <div class="mt-2 space-y-1 text-sm text-gray-700">
                                            @if ($transaction->reversal)
                                                <p><span class="font-semibold text-gray-900">Por:</span> {{ $transaction->reversal->user?->name ?? '-' }}</p>
                                                <p><span class="font-semibold text-gray-900">Motivo:</span> {{ $transaction->reversal->reason ?? '-' }}</p>
                                            @else
                                                <p>Nenhuma reversao registrada.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif

                    @if ($reversingTransactionId === $transaction->id)
                        <tr wire:key="transaction-reversal-row-{{ $transaction->id }}" class="bg-red-50">
                            <td colspan="6" class="px-6 py-4">
                                <form wire:submit="reverse" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                                    <div class="flex-1">
                                        <x-input-label for="reason-{{ $transaction->id }}" value="Motivo da reversao" />
                                        <x-text-input
                                            id="reason-{{ $transaction->id }}"
                                            wire:model="reason"
                                            class="mt-1 block w-full"
                                            placeholder="Descreva o motivo"
                                        />
                                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                    </div>

                                    <div class="flex gap-2 pt-6">
                                        <x-danger-button>
                                            Confirmar
                                        </x-danger-button>
                                        <x-secondary-button type="button" wire:click="cancelReversal">
                                            Cancelar
                                        </x-secondary-button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Nenhuma transacao encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
