<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->after('direccion')->constrained('departamentos');
            $table->foreignId('ciudad_id')->nullable()->after('departamento_id')->constrained('ciudades');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
            $table->dropForeign(['departamento_id']);
            $table->dropColumn(['ciudad_id', 'departamento_id']);
        });
    }
};
