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
            $table->string('cover_image_url')->nullable()->after('embed_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spotify_sections', function (Blueprint $table) {
            $table->dropColumn('cover_image_url');
        });
    }
};