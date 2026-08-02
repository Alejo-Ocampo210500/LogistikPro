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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('plan_id')->constrained('planes');
            $table->foreignId('estado_id')->constrained('estados');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_final')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->string('usuarios_contratados')->nullable();
            $table->decimal('valor_pagado')->nullable();
            $table->boolean('renovacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
