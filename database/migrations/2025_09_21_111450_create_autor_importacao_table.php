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
        Schema::create('autor_importacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacao_id')->nullable()->constrained('importacoes');
            $table->foreignId('autor_id')->nullable()->constrained('autores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autor_importacao');
    }
};
