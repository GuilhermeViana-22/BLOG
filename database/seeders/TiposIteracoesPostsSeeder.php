<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposIteracoesPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserções na tabela tipos__iteracoes__posts
        DB::table('tipos__iteracoes__posts')->insert([
            ['tipo' => 'cadastrou', 'ativo' => 1],
            ['tipo' => 'editou', 'ativo' => 1],
            ['tipo' => 'deletou', 'ativo' => 1],
            ['tipo' => 'comentou', 'ativo' => 1],
            ['tipo' => 'postou', 'ativo' => 1],
        ]);
    }
}
