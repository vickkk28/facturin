@csrf

@php
    $input = 'w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white';
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="razon_social" class="mb-1 block text-sm font-medium">Razón social *</label>
        <input id="razon_social" name="razon_social" type="text"
               value="{{ old('razon_social', $cliente->razon_social) }}" class="{{ $input }}">
        @error('razon_social') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="cuit" class="mb-1 block text-sm font-medium">CUIT * <span class="text-neutral-500">(sin guiones)</span></label>
        <input id="cuit" name="cuit" type="text" inputmode="numeric" maxlength="11" placeholder="20123456789"
               value="{{ old('cuit', $cliente->cuit) }}" class="{{ $input }}">
        @error('cuit') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="condicion_iva" class="mb-1 block text-sm font-medium">Condición frente al IVA *</label>
        <select id="condicion_iva" name="condicion_iva" class="{{ $input }}">
            <option value="">Elegí una opción</option>
            @foreach (\App\Models\Cliente::CONDICIONES_IVA as $valor => $texto)
                <option value="{{ $valor }}" @selected(old('condicion_iva', $cliente->condicion_iva) === $valor)>
                    {{ $texto }}
                </option>
            @endforeach
        </select>
        @error('condicion_iva') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-1 block text-sm font-medium">Email</label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $cliente->email) }}" class="{{ $input }}">
        @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="telefono" class="mb-1 block text-sm font-medium">Teléfono</label>
        <input id="telefono" name="telefono" type="text"
               value="{{ old('telefono', $cliente->telefono) }}" class="{{ $input }}">
        @error('telefono') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="domicilio" class="mb-1 block text-sm font-medium">Domicilio</label>
        <input id="domicilio" name="domicilio" type="text"
               value="{{ old('domicilio', $cliente->domicilio) }}" class="{{ $input }}">
        @error('domicilio') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
        {{ $boton }}
    </button>
    <a href="{{ route('clientes.index') }}" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm hover:bg-neutral-100 dark:border-neutral-600 dark:hover:bg-neutral-800">
        Cancelar
    </a>
</div>