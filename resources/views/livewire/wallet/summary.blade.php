<section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Saldo disponivel</p>
            <div class="mt-2 text-4xl font-semibold text-gray-950">
                R$ {{ number_format((float) $wallet->balance, 2, ',', '.') }}
            </div>
        </div>

        <div class="rounded-md bg-gray-100 px-4 py-3 text-sm text-gray-700">
            Carteira #{{ $wallet->id }}
        </div>
    </div>
</section>
