<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'image_url',
        'user_id',
        'category_id',
        'type_id',
        'can_be_commented',
    ];

    protected $casts = [
        'links' => 'array',
        'tags_id' => 'array',
        'can_be_commented' => 'boolean',
    ];

    /**
     * Define o relacionamento com o modelo Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
