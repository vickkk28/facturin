<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    // Punto de venta fijo para todos los comprobantes
    public const PUNTO_VENTA = 1;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'punto_venta',
        'numero',
        'fecha',
        'importe',
        'concepto',
    ];

    // Convierte la fecha en objeto fecha y el importe a 2 decimales
    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'importe' => 'decimal:2',
        ];
    }

    // Cada comprobante pertenece a un cliente
    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    // Regla de negocio: qué tipo de factura corresponde
    // Si el emisor es Responsable Inscripto: A para otro RI, B para el resto
    // Si el emisor es Monotributista o Exento: siempre C
    public static function tipoPara(Cliente $cliente, string $condicionEmisor = 'responsable_inscripto'): string
    {
        if ($condicionEmisor !== 'responsable_inscripto') {
            return 'C';
        }

        return $cliente->condicion_iva === 'responsable_inscripto' ? 'A' : 'B';
    }

    // Devuelve el número con formato ARCA, ej: "A 0001-00000005"
    public function numeroCompleto(): string
    {
        return sprintf('%s %04d-%08d', $this->tipo, $this->punto_venta, $this->numero);
    }
}
