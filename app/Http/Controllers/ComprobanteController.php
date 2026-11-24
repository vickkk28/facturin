<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comprobante;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComprobanteController extends Controller
{
    // Lista los comprobantes con el nombre del cliente (usando JOIN)
    public function index(Request $request): View
    {
        $tipo = $request->input('tipo');

        $comprobantes = Comprobante::query()
            ->join('clientes', 'clientes.id', '=', 'comprobantes.cliente_id')
            ->select('comprobantes.*', 'clientes.razon_social', 'clientes.cuit')
            ->when($tipo, function ($query, $tipo) {
                $query->where('comprobantes.tipo', $tipo);
            })
            ->orderByDesc('comprobantes.fecha')
            ->orderByDesc('comprobantes.numero')
            ->paginate(15)
            ->withQueryString();

        return view('comprobantes.index', compact('comprobantes', 'tipo'));
    }

    // Muestra el formulario para emitir un comprobante
    public function create(): View
    {
        $clientes = Cliente::orderBy('razon_social')->get();

        return view('comprobantes.create', compact('clientes'));
    }

    // Guarda el comprobante: el tipo y el número se calculan solos
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'importe' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'concepto' => ['nullable', 'string', 'max:255'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'cliente_id.exists' => 'El cliente elegido no existe.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'importe.numeric' => 'El importe tiene que ser un número.',
            'importe.min' => 'El importe tiene que ser mayor a 0.',
        ]);

        $cliente = Cliente::findOrFail((int) $datos['cliente_id']);
        $tipo = Comprobante::tipoPara($cliente);

        // Transacción: busca el último número y guarda el nuevo como una sola operación,
        // así dos usuarios emitiendo a la vez no pueden obtener el mismo número
        $comprobante = DB::transaction(function () use ($datos, $tipo) {
            $ultimo = Comprobante::where('tipo', $tipo)
                ->where('punto_venta', Comprobante::PUNTO_VENTA)
                ->lockForUpdate()
                ->max('numero');

            return Comprobante::create([
                ...$datos,
                'tipo' => $tipo,
                'punto_venta' => Comprobante::PUNTO_VENTA,
                'numero' => (int) $ultimo + 1,
            ]);
        });

        return redirect()->route('comprobantes.index')
            ->with('status', "Comprobante {$comprobante->numeroCompleto()} emitido correctamente.");
    }
}
