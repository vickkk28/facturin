<x-layouts::app :title="__('Nuevo cliente')">
    <div class="mx-auto w-full max-w-2xl">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo cliente</h1>

        <form method="POST" action="{{ route('clientes.store') }}"
              class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            @include('clientes.form', ['boton' => 'Guardar cliente'])
        </form>
    </div>
</x-layouts::app>