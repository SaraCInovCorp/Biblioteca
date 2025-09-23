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
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_request_item_id')->constrained('book_request_items');
            $table->foreignId('livro_id')->constrained('livros');
            $table->foreignId('user_id')->constrained('users');
            $table->text('review_text');
            $table->enum('status', ['suspenso', 'ativo', 'recusado'])->default('suspenso');
            $table->text('admin_justification')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
