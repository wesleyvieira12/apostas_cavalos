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
        Schema::create('corridas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->dateTime('data');
            $table->decimal('taxa', 5, 2); // porcentagem (ex: 5.50%)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corridas');
    }
};
