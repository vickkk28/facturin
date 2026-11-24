<x-layouts::app :title="__('Dashboard')">
    @php
        $pesos = fn ($monto) => '$ '.number_format((float) $monto, 2, ',', '.');
        $card = 'rounded-xl border border-neutral-200 p-5 dark:border-neutral-700';
    @endphp

    <div class="flex w-full flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <a href="{{ route('comprobantes.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Emitir comprobante
            </a>
        </div>

        {{-- Tarjetas con los números principales --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="{{ $card }}">
                <p class="text-sm text-neutral-500">Facturado este mes</p>
                <p class="mt-2 font-mono text-2xl font-semibold">{{ $pesos($totalMes) }}</p>
            </div>
            <div class="{{ $card }}">
                <p class="text-sm text-neutral-500">Comprobantes este mes</p>
                <p class="mt-2 text-2xl font-semibold">{{ $cantidadMes }}</p>
            </div>
            <div class="{{ $card }}">
                <p class="text-sm text-neutral-500">Clientes</p>
                <p class="mt-2 text-2xl font-semibold">{{ $cantidadClientes }}</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Gráfico de barras de los últimos 6 meses --}}
            <div class="{{ $card }} lg:col-span-2">
                <h2 class="mb-4 font-medium">Facturación últimos 6 meses</h2>
                <div class="flex h-48 items-end gap-3">
                    @foreach ($meses as $mes)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-xs text-neutral-500">
                                {{ $mes['total'] > 0 ? number_format($mes['total'] / 1000, 0, ',', '.').'k' : '' }}
                            </span>
                            <div class="w-full rounded-t-md bg-blue-600"
                                 style="height: {{ max(2, round($mes['total'] / $maximoMes * 100)) }}%"
                                 title="{{ $pesos($mes['total']) }}"></div>
                            <span class="text-xs text-neutral-500">{{ $mes['nombre'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Resumen por tipo de factura --}}
            <div class="{{ $card }}">
                <h2 class="mb-4 font-medium">Por tipo de factura</h2>
                @forelse ($porTipo as $fila)
                    <div class="flex items-center justify-between border-b border-neutral-200 py-2 last:border-0 dark:border-neutral-700">
                        <span>
                            <strong>Factura {{ $fila->tipo }}</strong>
                            <span class="text-sm text-neutral-500">({{ $fila->cantidad }})</span>
                        </span>
                        <span class="font-mono text-sm">{{ $pesos($fila->total) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-500">Todavía no hay comprobantes.</p>
                @endforelse
            </div>
        </div>

        {{-- Ranking de clientes --}}
        <div class="{{ $card }}">
            <h2 class="mb-4 font-medium">Clientes con mayor facturación</h2>
            @if ($topClientes->isEmpty())
                <p class="text-sm text-neutral-500">Todavía no hay comprobantes.</p>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="text-neutral-500">
                        <tr>
                            <th class="pb-2 font-medium">#</th>
                            <th class="pb-2 font-medium">Cliente</th>
                            <th class="pb-2 font-medium">Comprobantes</th>
                            <th class="pb-2 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($topClientes as $i => $cliente)
                            <tr>
                                <td class="py-2 text-neutral-500">{{ $i + 1 }}</td>
                                <td class="py-2">{{ $cliente->razon_social }}</td>
                                <td class="py-2">{{ $cliente->cantidad }}</td>
                                <td class="py-2 text-right font-mono">{{ $pesos($cliente->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-layouts::app>