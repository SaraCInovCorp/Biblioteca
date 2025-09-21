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
        Schema::table('livros', function (Blueprint $table) {
            $table->string('origem')->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->constrained()->after('origem');
        });

        Schema::table('editoras', function (Blueprint $table) {
            $table->string('origem')->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->constrained()->after('origem');
        });

        Schema::table('autores', function (Blueprint $table) {
            $table->string('origem')->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->constrained()->after('origem');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn('origem');
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('editoras', function (Blueprint $table) {
            $table->dropColumn('origem');
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('autores', function (Blueprint $table) {
            $table->dropColumn('origem');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
