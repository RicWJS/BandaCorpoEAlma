<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Adiciona a coluna de visualizações depois da coluna 'status'
            $table->integer('views_count')->default(0)->after('status');
            
            // Adiciona a chave estrangeira para categoria depois da coluna 'id'
            $table->unsignedBigInteger('category_id')->nullable()->after('id');

            // Define o relacionamento e a ação em caso de deleção da categoria
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null'); // Se uma categoria for deletada, os posts associados terão category_id = null
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna em ordem reversa
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropColumn('views_count');
        });
    }
};
