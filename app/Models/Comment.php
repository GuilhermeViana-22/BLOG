<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model{
    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'gif_url',
        'parent_comment_id',
        'is_reply',
    ];

    // Define o relacionamento belongsTo com o Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Adiciona também o relacionamento com o usuário, se aplicável
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
