<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionando coluna para foto
            $table->string('photo')->nullable()->after('password');

            // Adicionando novas colunas
            $table->string('location')->nullable()->after('photo');
            $table->text('about_me')->nullable()->after('location');
            $table->string('website')->nullable()->after('about_me');
            $table->string('instagram')->nullable()->after('website');
            $table->string('github')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('github');
            $table->string('youtube')->nullable()->after('linkedin');
            $table->string('reddit')->nullable()->after('youtube');
            $table->text('others')->nullable()->after('reddit');
            $table->integer('followers_count')->nullable()->after('others');
            $table->integer('following_count')->nullable()->after('followers_count');
            $table->enum('badge', ['beginner', 'intermediate', 'superstar'])->nullable()->after('following_count');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover as novas colunas
            $table->dropColumn([
                'photo',
                'location',
                'about_me',
                'website',
                'instagram',
                'github',
                'linkedin',
                'youtube',
                'reddit',
                'others',
                'followers_count',
                'following_count',
                'badge',
            ]);
        });
    }

};
