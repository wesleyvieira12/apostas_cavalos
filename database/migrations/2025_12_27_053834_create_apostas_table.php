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
        Schema::create('apostas', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('apostador_id')
                ->constrained('apostadores')
                ->cascadeOnDelete();
            $table->foreignId('corrida_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('rodada');
            $table->integer('animal');
            $table->decimal('valor', 10, 2);
            $table->decimal('lo', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apostas');
    }
};
