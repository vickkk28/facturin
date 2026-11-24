<x-layouts::app :title="$cliente->razon_social">
    @php
        $pesos = fn ($monto) => '$ '.number_format((float) $monto, 2, ',', '.');
        $card = 'rounded-xl border border-neutral-200 p-5 dark:border-neutral-700';
        $input = 'rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white';
        $boton = 'rounded-lg border border-neutral-300 px-3 py-1.5 text-sm hover:bg-neutral-100 dark:border-neutral-600 dark:hover:bg-neutral-800';
        $cuit = substr($cliente->cuit, 0, 2).'-'.substr($cliente->cuit, 2, 8).'-'.substr($cliente->cuit, 10, 1);
    @endphp

    <div class="flex w-full flex-col gap-6">

        {{-- Encabezado con los datos del cliente --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('clientes.index') }}" class="text-sm text-neutral-500 hover:underline">← Volver a clientes</a>
                <h1 class="mt-1 text-2xl font-semibold">{{ $cliente->razon_social }}</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    CUIT <span class="font-mono">{{ $cuit }}</span>
                    · {{ \App\Models\Cliente::CONDICIONES_IVA[$cliente->condicion_iva] }}
                    @if ($cliente->email) · {{ $cliente->email }} @endif
                </p>
            </div>
            <a href="{{ route('clientes.edit', $cliente) }}" class="{{ $boton }}">Editar datos</a>
        </div>

        {{-- Filtro por fechas --}}
        <div class="{{ $card }}">
            <form method="GET" action="{{ route('clientes.show', $cliente) }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="desde" class="mb-1 block text-sm font-medium">Desde</label>
                    <input id="desde" name="desde" type="date" value="{{ $desde }}" class="{{ $input }}">
                </div>
                <div>
                    <label for="hasta" class="mb-1 block text-sm font-medium">Hasta</label>
                    <input id="hasta" name="hasta" type="date" value="{{ $hasta }}" class="{{ $input }}">
                </div>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Filtrar
                </button>
            </form>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('clientes.show', [$cliente, 'desde' => now()->startOfMonth()->toDateString(), 'hasta' => now()->toDateString()]) }}" class="{{ $boton }}">Este mes</a>
                <a href="{{ route('clientes.show', [$cliente, 'desde' => now()->startOfYear()->toDateString(), 'hasta' => now()->toDateString()]) }}" class="{{ $boton }}">Este año</a>
                <a href="{{ route('clientes.show', $cliente) }}" class="{{ $boton }}">Todo</a>
            </div>
        </div>

        {{-- Totales del período --}}
        <div class="grid gap-4 md:grid-cols-2">
            <div class="{{ $card }}">
                <p class="text-sm text-neutral-500">
                    Total facturado
                    @if ($desde || $hasta)
                        ({{ $desde ? \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') : 'inicio' }}
                        al {{ $hasta ? \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }})
                    @else
                        (histórico)
                    @endif
                </p>
                <p class="mt-2 font-mono text-3xl font-semibold">{{ $pesos($total) }}</p>
            </div>
            <div class="{{ $card }}">
                <p class="text-sm text-neutral-500">Comprobantes en el período</p>
                <p class="mt-2 text-3xl font-semibold">{{ $comprobantes->count() }}</p>
            </div>
        </div>

        {{-- Lista de comprobantes del cliente --}}
        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Número</th>
                        <th class="px-4 py-3 font-medium">Fecha</th>
                        <th class="px-4 py-3 font-medium">Concepto</th>
                        <th class="px-4 py-3 text-right font-medium">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($comprobantes as $comprobante)
                        <tr>
                            <td class="px-4 py-3 font-mono">{{ $comprobante->numeroCompleto() }}</td>
                            <td class="px-4 py-3">{{ $comprobante->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $comprobante->concepto ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono">{{ $pesos($comprobante->importe) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-500">
                                No hay comprobantes en este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>