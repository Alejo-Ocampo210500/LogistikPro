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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('cliente_id')->constrained('clientes')->nullable();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago');
            $table->bigInteger('numero_venta');
            $table->timestamp('fecha_venta');
            $table->decimal('subtotal', 10, 0);
            $table->decimal('descuento', 10, 0);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 0);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado', 'anulado', 'pago parcial', 'reembolsado', 'en proceso'])->default('pagado');
            $table->string('observaciones')->nullable();
            $table->string('codigo_barra')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
