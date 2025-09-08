<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('isbn')->unique();
            $table->foreignIdFor(Editora::class)->constrained('editoras')->onDelete('cascade');
            $table->text('bibliografia')->nullable();
            $table->string('capa_url')->nullable();
            $table->decimal('preco', 8, 2)->nullable();
            $table->enum('status',['disponivel','indisponivel','requisitado']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
