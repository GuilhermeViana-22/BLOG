<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sutitile')->nullable();
            $table->text('body');
            $table->string('footer')->nullable();
            $table->string('links')->nullable();
            $table->string('tags_id'); // #por exemplo : #php, #aravel
            $table->string('comment');
            $table->string('image_url')->nullable();
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('type_id');
            $table->boolean('can_be_commented');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
