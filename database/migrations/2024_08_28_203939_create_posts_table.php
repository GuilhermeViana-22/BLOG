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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('body');
            $table->string('footer')->nullable();
            $table->text('links')->nullable()->change();
            $table->text('tags_id')->nullable()->change();
            $table->string('image_url')->nullable();
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('type_id');
            $table->boolean('can_be_commented')->default(0);
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
