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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos');
            $table->string('numero_documento');
            $table->string('codigo_verificacion')->nullable();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->string('sitio_web')->nullable();
            $table->foreignId('pais_id')->constrained('paises');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->foreignId('ciudad_id')->constrained('ciudades');
            $table->string('codigo_postal')->nullable();
            $table->decimal('cupo_credito', 15, 2)->default(0)->nullable();
            $table->foreignId('estado_id')->constrained('estados');
            $table->integer('dias_credito')->default(0)->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('proveedores');
    }
};
