<x-layouts::app :title="__('Clientes')">
    <div class="flex w-full flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Clientes</h1>
            <a href="{{ route('clientes.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Nuevo cliente
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('clientes.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $busqueda }}" placeholder="Buscar por razón social o CUIT…"
                   class="w-full max-w-sm rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            <button type="submit" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm hover:bg-neutral-100 dark:border-neutral-600 dark:hover:bg-neutral-800">
                Buscar
            </button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Razón social</th>
                        <th class="px-4 py-3 font-medium">CUIT</th>
                        <th class="px-4 py-3 font-medium">Condición IVA</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td class="px-4 py-3">{{ $cliente->razon_social }}</td>
                            <td class="px-4 py-3 font-mono">
                                {{ substr($cliente->cuit, 0, 2) }}-{{ substr($cliente->cuit, 2, 8) }}-{{ substr($cliente->cuit, 10, 1) }}
                            </td>
                            <td class="px-4 py-3">{{ \App\Models\Cliente::CONDICIONES_IVA[$cliente->condicion_iva] }}</td>
                            <td class="px-4 py-3">{{ $cliente->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-600 hover:underline dark:text-blue-400">Editar</a>
                                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                          onsubmit="return confirm('¿Eliminar a {{ $cliente->razon_social }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline dark:text-red-400">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500">
                                {{ $busqueda ? 'No hay clientes que coincidan con la búsqueda.' : 'Todavía no cargaste clientes.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $clientes->links() }}
    </div>
</x-layouts::app>