<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id'); // ID do post relacionado
            $table->integer('user_id'); // ID do usuário que fez o comentário
            $table->text('comment')->nullable(); // Texto do comentário (opcional)
            $table->string('gif_url')->nullable(); // URL do GIF (opcional)
            $table->unsignedBigInteger('parent_comment_id')->nullable(); // ID do comentário que foi respondido, se houver
            $table->integer('likes_count')->default(0); // Quantidade de curtidas no comentário
            $table->boolean('is_reply')->default(false); // Indica se é uma resposta a outro comentário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
