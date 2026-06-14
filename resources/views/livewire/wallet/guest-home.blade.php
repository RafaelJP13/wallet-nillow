<div class="space-y-8">
    <div class="space-y-6">
        <div class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">
            Wallet
        </div>

        <div class="space-y-3">
            <h1 class="text-4xl font-semibold tracking-normal text-gray-950">
                Sua carteira digital
            </h1>
            <p class="text-base leading-7 text-gray-600">
                Entre para consultar saldo, fazer depositos, transferir valores e acompanhar o historico de transacoes.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                Entrar
            </a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-800 transition hover:bg-gray-50">
                Criar conta
            </a>
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-md border border-gray-200 bg-white p-4">
            <div class="text-xs font-medium uppercase text-gray-500">Depositos</div>
            <div class="mt-2 text-base font-semibold text-gray-900">Instantaneos</div>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-4">
            <div class="text-xs font-medium uppercase text-gray-500">Transferencias</div>
            <div class="mt-2 text-base font-semibold text-gray-900">Entre carteiras</div>
        </div>
    </div>
</div>
