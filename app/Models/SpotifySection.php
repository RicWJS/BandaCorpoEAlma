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
            // Renomeia as colunas existentes para nomes mais claros e padronizados
            $table->renameColumn('spotify_link', 'embed_code');
            $table->renameColumn('cover_image_path', 'manual_cover_image_path');

            // Adiciona a coluna para o título e a nova URL da capa da API
            $table->string('title')->after('id');
            $table->string('cover_image_url')->nullable()->after('embed_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spotify_sections', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('cover_image_url');
            
            // Reverte os nomes das colunas
            $table->renameColumn('embed_code', 'spotify_link');
            $table->renameColumn('manual_cover_image_path', 'cover_image_path');
        });
    }
};