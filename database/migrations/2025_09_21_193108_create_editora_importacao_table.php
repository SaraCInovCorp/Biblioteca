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
        Schema::create('editora_importacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacao_id')->nullable()->constrained('importacoes');
            $table->foreignId('editora_id')->nullable()->constrained('editoras');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editora_importacao');
    }
};
