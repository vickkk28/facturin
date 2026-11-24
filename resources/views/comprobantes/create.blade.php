<x-layouts::app :title="__('Nuevo comprobante')">
    @php
        $input = 'w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white';
    @endphp

    <div class="mx-auto w-full max-w-2xl">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo comprobante</h1>

        @if ($clientes->isEmpty())
            <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                Primero tenés que <a href="{{ route('clientes.create') }}" class="underline">cargar un cliente</a>.
            </div>
        @else
            <form method="POST" action="{{ route('comprobantes.store') }}"
                  class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700"
                  x-data="{ tipo: '' }">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="cliente_id" class="mb-1 block text-sm font-medium">Cliente *</label>
                        <select id="cliente_id" name="cliente_id" class="{{ $input }}"
                                x-init="tipo = $el.selectedOptions[0]?.dataset.tipo ?? ''"
                                @change="tipo = $event.target.selectedOptions[0].dataset.tipo ?? ''">
                            <option value="">Elegí un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                        data-tipo="{{ \App\Models\Comprobante::tipoPara($cliente) }}"
                                        @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->razon_social }} — {{ \App\Models\Cliente::CONDICIONES_IVA[$cliente->condicion_iva] }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror

                        <p x-show="tipo" x-cloak class="mt-2 text-sm text-neutral-500">
                            Según la condición de IVA del cliente, corresponde
                            <strong class="text-blue-600 dark:text-blue-400">Factura <span x-text="tipo"></span></strong>.
                        </p>
                    </div>

                    <div>
                        <label for="fecha" class="mb-1 block text-sm font-medium">Fecha *</label>
                        <input id="fecha" name="fecha" type="date"
                               value="{{ old('fecha', now()->format('Y-m-d')) }}" class="{{ $input }}">
                        @error('fecha') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="importe" class="mb-1 block text-sm font-medium">Importe total ($) *</label>
                        <input id="importe" name="importe" type="number" step="0.01" min="0.01"
                               value="{{ old('importe') }}" class="{{ $input }}">
                        @error('importe') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="concepto" class="mb-1 block text-sm font-medium">Concepto</label>
                        <input id="concepto" name="concepto" type="text" placeholder="Ej: Honorarios noviembre"
                               value="{{ old('concepto') }}" class="{{ $input }}">
                        @error('concepto') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Emitir comprobante
                    </button>
                    <a href="{{ route('comprobantes.index') }}" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm hover:bg-neutral-100 dark:border-neutral-600 dark:hover:bg-neutral-800">
                        Cancelar
                    </a>
                </div>
            </form>
        @endif
    </div>
</x-layouts::app>