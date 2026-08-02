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
        Schema::create('productos', function (Blueprint $table) {

            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('codigo')->unique();
            $table->string('codigo_barras')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('marca_id')->nullable()->constrained('marcas');
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida');
            $table->foreignId('impuesto_id')->nullable()->constrained('impuestos');
            $table->decimal('costo', 15, 2)->default(0);
            $table->decimal('precio_venta', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->nullable();
            $table->boolean('maneja_inventario')->default(true);
            $table->boolean('permite_descuento')->default(true);
            $table->boolean('es_servicio')->default(false);
            $table->boolean('venta_libre')->default(true);
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('actualizado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
