<?php
// app/Models/SpotifySection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotifySection extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_visible',
        'cover_image_path',
        'spotify_link',
    ];
}