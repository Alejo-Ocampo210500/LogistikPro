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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->enum('tipo_persona', ['juridica', 'natural']);
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos');
            $table->string('numero_documento')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('email')->unique();
            $table->string('celular');
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('pais_id')->constrained('paises');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->foreignId('ciudad_id')->constrained('ciudades');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino'])->nullable();
            $table->decimal('limite_credito', 15, 2)->nullable();
            $table->decimal('saldo_credito', 15, 2)->nullable();
            $table->integer('dias_credito')->nullable();
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('actualizado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
