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
        Schema::table('spotify_sections', function (Blueprint $table) {
            // 1. Renomeia a coluna do link
            $table->renameColumn('spotify_link', 'embed_link');

            // 2. Adiciona a nova coluna para a URL da imagem do artista
            $table->string('cover_image_url')->nullable()->after('embed_link');

            // 3. Remove a coluna antiga de upload manual
            $table->dropColumn('cover_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spotify_sections', function (Blueprint $table) {
            // Reverte as operações na ordem inversa
            $table->string('cover_image_path')->nullable();
            $table->dropColumn('cover_image_url');
            $table->renameColumn('embed_link', 'spotify_link');
        });
    }
};