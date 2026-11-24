<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // corre las migraciones.

    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('cuit', 11)->unique();
            $table->enum('condicion_iva', [
                'responsable_inscripto',
                'monotributista',
                'exento',
                'consumidor_final',
            ]);
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('domicilio')->nullable();
            $table->timestamps();
        });
    }

    // revierte las migraciones.

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
