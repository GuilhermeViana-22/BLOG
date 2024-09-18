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
            $table->integer('user_id'); // ID do usu�rio que fez o coment�rio
            $table->text('comment')->nullable(); // Texto do coment�rio (opcional)
            $table->string('gif_url')->nullable(); // URL do GIF (opcional)
            $table->unsignedBigInteger('parent_comment_id')->nullable(); // ID do coment�rio que foi respondido, se houver
            $table->integer('likes_count')->default(0); // Quantidade de curtidas no coment�rio
            $table->boolean('is_reply')->default(false); // Indica se � uma resposta a outro coment�rio
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
