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
        Schema::create('notificaciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('evento', 80);
            $table->string('tipo', 40)->default('sistema');
            $table->string('severidad', 30)->default('info');
            $table->string('titulo', 160);
            $table->text('mensaje');
            $table->string('icono', 80)->nullable();
            $table->string('destino_modulo', 120)->default('notificaciones-sistema');
            $table->string('destino_id', 120)->nullable();
            $table->string('hash_evento', 191)->nullable()->unique();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('usuario_actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('destino_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['evento', 'created_at']);
            $table->index(['severidad', 'created_at']);
            $table->index(['empresa_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
