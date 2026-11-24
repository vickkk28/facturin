<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    // Lista los clientes, con búsqueda y paginado
    public function index(Request $request)
    {
        $busqueda = $request->input('q');

        $clientes = Cliente::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('razon_social', 'like', "%{$busqueda}%")
                      ->orWhere('cuit', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('razon_social')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'busqueda'));
    }

    // Muestra el formulario vacío
    public function create()
    {
        return view('clientes.create', ['cliente' => new Cliente()]);
    }

    // Guarda un cliente nuevo
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Cliente::create($datos);

        return redirect()->route('clientes.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    // Muestra el formulario con los datos del cliente
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    // Guarda los cambios
    public function update(Request $request, Cliente $cliente)
    {
        $datos = $this->validar($request, $cliente);

        $cliente->update($datos);

        return redirect()->route('clientes.index')
            ->with('status', 'Cliente actualizado correctamente.');
    }

    // Borra el cliente
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('status', 'Cliente eliminado.');
    }

    // Reglas de validación compartidas entre crear y editar
    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        return $request->validate([
            'razon_social'  => ['required', 'string', 'max:255'],
            'cuit'          => ['required', 'digits:11', Rule::unique('clientes', 'cuit')->ignore($cliente?->id)],
            'condicion_iva' => ['required', Rule::in(array_keys(Cliente::CONDICIONES_IVA))],
            'email'         => ['nullable', 'email', 'max:255'],
            'telefono'      => ['nullable', 'string', 'max:50'],
            'domicilio'     => ['nullable', 'string', 'max:255'],
        ], [
            'required'    => 'Este campo es obligatorio.',
            'email'       => 'Ingresá un email válido.',
            'max'         => 'Es demasiado largo.',
            'cuit.digits' => 'El CUIT debe tener 11 números, sin guiones.',
            'cuit.unique' => 'Ya existe un cliente con ese CUIT.',
        ]);
    }
}