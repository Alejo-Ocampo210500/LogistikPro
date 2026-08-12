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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('nit');
            $table->string('direccion');
            $table->string('telefono')->nullable();
            $table->string('email');
            $table->foreignId('ciudad_id')->constrained('ciudades');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->foreignId('pais_id')->constrained('paises');
            $table->string('responsable');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
