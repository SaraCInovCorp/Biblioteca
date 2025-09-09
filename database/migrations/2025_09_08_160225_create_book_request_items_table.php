<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Livro;
use App\Models\BookRequest;
use App\Models\BookRequestItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_request_items', function (Blueprint $table) {
            $table->id();
            $table->date('data_real_entrega')->nullable();
            $table->integer('dias_decorridos')->nullable();
            $table->enum('status',['cancelada','realizada','entregue_ok','entregue_obs','nao_entregue']);
            $table->foreignIdFor(Livro::class)->constrained('livros');
            $table->foreignIdFor(BookRequest::class)->constrained('book_requests');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_request_items');
    }
};
