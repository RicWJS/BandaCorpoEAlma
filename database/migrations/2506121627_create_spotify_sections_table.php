<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_spotify_sections_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_sections', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_visible')->default(true);
            $table->string('cover_image_path')->nullable();
            $table->text('spotify_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_sections');
    }
};