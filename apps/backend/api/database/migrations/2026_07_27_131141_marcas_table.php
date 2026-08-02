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
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('logo')->nullable();
            $table->string('sitio_web')->nullable();
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
        Schema::dropIfExists('marcas');
    }
};
