<x-layouts::app :title="__('Comprobantes')">
    <div class="flex w-full flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Comprobantes</h1>
            <a href="{{ route('comprobantes.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Nuevo comprobante
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex gap-2 text-sm">
            @foreach (['' => 'Todos', 'A' => 'Factura A', 'B' => 'Factura B', 'C' => 'Factura C'] as $valor => $texto)
                <a href="{{ route('comprobantes.index', $valor ? ['tipo' => $valor] : []) }}"
                   class="rounded-lg border px-3 py-1.5 {{ (string) $tipo === (string) $valor ? 'border-blue-600 bg-blue-600 text-white' : 'border-neutral-300 hover:bg-neutral-100 dark:border-neutral-600 dark:hover:bg-neutral-800' }}">
                    {{ $texto }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Número</th>
                        <th class="px-4 py-3 font-medium">Fecha</th>
                        <th class="px-4 py-3 font-medium">Cliente</th>
                        <th class="px-4 py-3 font-medium">Concepto</th>
                        <th class="px-4 py-3 text-right font-medium">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($comprobantes as $comprobante)
                        <tr>
                            <td class="px-4 py-3 font-mono">{{ $comprobante->numeroCompleto() }}</td>
                            <td class="px-4 py-3">{{ $comprobante->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                {{ $comprobante->razon_social }}
                                <span class="block font-mono text-xs text-neutral-500">
                                    {{ substr($comprobante->cuit, 0, 2) }}-{{ substr($comprobante->cuit, 2, 8) }}-{{ substr($comprobante->cuit, 10, 1) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $comprobante->concepto ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono">$ {{ number_format((float) $comprobante->importe, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500">
                                Todavía no hay comprobantes{{ $tipo ? ' de tipo '.$tipo : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $comprobantes->links() }}
    </div>
</x-layouts::app>