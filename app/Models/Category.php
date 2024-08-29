<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'title', 'description', 'relevant', 'published', 'parent_id'];

    /**
     * Define o relacionamento com o modelo Post.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Define o relacionamento com categorias pai.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Define o relacionamento com categorias filhas.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
