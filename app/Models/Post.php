<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // Definição dos campos que podem ser preenchidos em massa
    protected $fillable = [
        'title',
        'subtitle',
        'body',
        'footer',
        'links',
        'tags_id',
        'comment_id',
        'image_url',
        'user_id',
        'category_id',
        'type_id',
        'can_be_commented',
        'published',
    ];

    /**
     * Define o relacionamento com o modelo Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
