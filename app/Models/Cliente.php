<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    // Opciones de condición frente al IVA: valor en la base => texto en pantalla
    public const CONDICIONES_IVA = [
        'responsable_inscripto' => 'Responsable Inscripto',
        'monotributista' => 'Monotributista',
        'exento' => 'Exento',
        'consumidor_final' => 'Consumidor Final',
    ];

    // Campos que se pueden completar desde un formulario
    protected $fillable = [
        'razon_social',
        'cuit',
        'condicion_iva',
        'email',
        'telefono',
        'domicilio',
    ];

    // Un cliente tiene muchos comprobantes
    /** @return HasMany<Comprobante, $this> */
    public function comprobantes(): HasMany
    {
        return $this->hasMany(Comprobante::class);
    }
}
