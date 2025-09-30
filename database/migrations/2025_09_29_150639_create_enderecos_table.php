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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['pagamento', 'entrega'])->default('entrega');
            $table->string('logradouro');
            $table->string('numero');
            $table->string('andereco')->nullable();
            $table->string('freguesia')->nullable(); 
            $table->string('localidade');
            $table->string('distrito');
            $table->string('codigo_postal');
            $table->string('pais')->default('Portugal');
            $table->string('telemovel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
