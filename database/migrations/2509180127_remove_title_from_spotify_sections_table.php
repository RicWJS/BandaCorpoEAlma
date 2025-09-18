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
            // Apenas remove a coluna 'title' se ela existir
            if (Schema::hasColumn('spotify_sections', 'title')) {
                $table->dropColumn('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spotify_sections', function (Blueprint $table) {
            $table->string('title')->after('id')->nullable();
        });
    }
};