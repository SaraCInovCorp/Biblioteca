<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Livro;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livro_waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Livro::class)->constrained('livros');
            $table->foreignIdFor(User::class)->constrained('users');
            $table->boolean('ativo')->default(true);
            $table->timestamp('notificado_em')->nullable();
            $table->timestamps();

            $table->unique(['livro_id', 'user_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_waiting_lists');
    }
};
