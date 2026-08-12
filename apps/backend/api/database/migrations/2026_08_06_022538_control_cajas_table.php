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
        Schema::create('control_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('usuario_apertura_id')->constrained('users');
            $table->foreignId('usuario_cierre_id')->constrained('users');
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre');
            $table->time('hora_apertura');
            $table->time('hora_cierre');
            $table->decimal('monto_apertura', 15, 0);
            $table->decimal('monto_cierre', 15, 0);
            $table->decimal('efectivo_sistema', 15, 0);
            $table->decimal('efectivo_contado', 15, 0);
            $table->decimal('diferencia', 15, 0);
            $table->string('observaciones_apertura')->nullable();
            $table->string('observaciones_cierre')->nullable();
            $table->enum('estado', ['Abierta', 'Cerrada', 'Anulada'])->default('Abierta');
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
        Schema::dropIfExists('control_cajas');
    }
};
