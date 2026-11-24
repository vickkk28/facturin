<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'razon_social',
        'cuit',
        'condicion_iva',
        'email',
        'telefono',
        'domicilio',
    ];
}