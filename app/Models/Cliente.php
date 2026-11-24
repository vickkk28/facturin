<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public const CONDICIONES_IVA = [
        'responsable_inscripto' => 'Responsable Inscripto',
        'monotributista' => 'Monotributista',
        'exento' => 'Exento',
        'consumidor_final' => 'Consumidor Final',
    ];

    protected $fillable = [
        'razon_social',
        'cuit',
        'condicion_iva',
        'email',
        'telefono',
        'domicilio',
    ];
}
