<?php
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // ADICIONADO: Importar a classe Carbon

class Post extends Model
{
    use HasFactory;

    // MODIFICADO: Adicionado 'category_id' e 'views_count'
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail_path',
        'image_path',
        'status',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * ADICIONADO: Define o relacionamento: um post pertence a uma categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getStatusTextAttribute(): string
    {
        switch ($this->status) {
            case 'published':
                return 'Publicado';
            case 'draft':
                return 'Rascunho';
            default:
                return ucfirst($this->status);
        }
    }
    
    /**
     * ADICIONADO: Acessor para formatar a data de publicação.
     * Retorna uma data relativa (ex: 'há 2 dias') se for menor que 7 dias,
     * ou uma data completa (ex: '5 de Janeiro de 2025') caso contrário.
     */
    public function getPublishedAtFormattedAttribute()
    {
        if ($this->published_at->diffInDays(now()) < 7) {
            return ucfirst($this->published_at->diffForHumans());
        }

        return $this->published_at->translatedFormat('j \d\e F \d\e Y');
    }
}
