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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('data_nascimento')->nullable();
            $table->enum('tipo_documento', ['BI', 'CC', 'Passaporte'])->nullable();
            $table->string('numero_documento')->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_validade')->nullable();
            $table->string('entidade_emissora')->nullable();
            $table->string('nacionalidade')->default('Portuguesa');
            $table->enum('genero', ['Masculino', 'Feminino', 'Nao Definido', 'Outro'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
