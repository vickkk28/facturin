<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->enum('tipo', ['A', 'B', 'C']);
            $table->unsignedInteger('punto_venta');
            $table->unsignedBigInteger('numero');
            $table->date('fecha');
            $table->decimal('importe', 12, 2);
            $table->string('concepto')->nullable();
            $table->timestamps();

            $table->unique(['tipo', 'punto_venta', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
