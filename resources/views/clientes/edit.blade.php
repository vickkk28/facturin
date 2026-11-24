<x-layouts::app :title="__('Editar cliente')">
    <div class="mx-auto w-full max-w-2xl">
        <h1 class="mb-6 text-2xl font-semibold">Editar cliente</h1>

        <form method="POST" action="{{ route('clientes.update', $cliente) }}"
              class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            @method('PUT')
            @include('clientes.form', ['boton' => 'Guardar cambios'])
        </form>
    </div>
</x-layouts::app>