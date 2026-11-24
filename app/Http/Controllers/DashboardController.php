<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comprobante;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Nombres de los meses para los gráficos
    private const MESES = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public function __invoke(): View
    {
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        // Totales del mes actual (SUM y COUNT)
        $totalMes = (float) Comprobante::whereBetween('fecha', [$inicioMes, $finMes])->sum('importe');
        $cantidadMes = Comprobante::whereBetween('fecha', [$inicioMes, $finMes])->count();
        $cantidadClientes = Cliente::count();

        // Cantidad y total por tipo de factura (GROUP BY)
        $porTipo = Comprobante::query()
            ->select('tipo', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(importe) as total'))
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get();

        // Los 5 clientes que más facturaron (JOIN + GROUP BY)
        $topClientes = DB::table('comprobantes')
            ->join('clientes', 'clientes.id', '=', 'comprobantes.cliente_id')
            ->select('clientes.razon_social', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(comprobantes.importe) as total'))
            ->groupBy('clientes.id', 'clientes.razon_social')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Facturación de los últimos 6 meses
        // Se agrupa en PHP (no en SQL) para que funcione igual en MySQL y en SQLite
        $desde = now()->startOfMonth()->subMonths(5)->toDateString();

        $porMes = Comprobante::where('fecha', '>=', $desde)
            ->get(['fecha', 'importe'])
            ->groupBy(fn (Comprobante $c): string => Carbon::parse($c->fecha)->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('importe'));

        $meses = collect(range(5, 0))->map(function (int $hace) use ($porMes) {
            $mes = now()->startOfMonth()->subMonths($hace);

            return [
                'nombre' => self::MESES[$mes->month - 1].' '.$mes->year,
                'total' => $porMes->get($mes->format('Y-m'), 0.0),
            ];
        });

        $maximoMes = max(1, $meses->max('total'));

        return view('dashboard', compact(
            'totalMes',
            'cantidadMes',
            'cantidadClientes',
            'porTipo',
            'topClientes',
            'meses',
            'maximoMes',
        ));
    }
}
