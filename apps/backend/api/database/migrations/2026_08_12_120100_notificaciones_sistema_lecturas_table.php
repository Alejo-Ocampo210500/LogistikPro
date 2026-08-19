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
        Schema::create('notificaciones_sistema_lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notificacion_id')->constrained('notificaciones_sistema')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('leida_at');
            $table->timestamps();

            $table->unique(['notificacion_id', 'user_id']);
            $table->index(['user_id', 'leida_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema_lecturas');
    }
};
