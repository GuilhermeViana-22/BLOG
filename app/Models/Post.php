<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'comment', 'user_id', 'category_id', 'published'];

    /**
     * Define o relacionamento com o modelo Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
