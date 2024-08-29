<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Linguagens de Programação' => ['Elixir', 'Go', 'Pascal', 'Python', 'JavaScript', 'Java', 'C++', 'Ruby'],
            'Desenvolvimento Web' => ['HTML', 'CSS', 'PHP', 'Laravel', 'React', 'Vue.js', 'Angular'],
            'Banco de Dados' => ['MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis'],
            'DevOps e Infraestrutura' => ['Docker', 'Kubernetes', 'Ansible', 'Terraform', 'AWS', 'Azure'],
            'Inteligência Artificial e Machine Learning' => ['TensorFlow', 'PyTorch', 'Keras', 'Scikit-learn', 'OpenCV'],
            'Tecnologias Emergentes' => ['Blockchain', 'Quantum Computing', 'Internet das Coisas (IoT)', 'Realidade Aumentada (AR)', 'Realidade Virtual (VR)']
        ];

        foreach ($categories as $parentTitle => $subCategories) {
            $parentCategory = Category::create([
                'name' => $parentTitle,
                'title' => $parentTitle,
                'description' => 'Categoria principal: ' . $parentTitle,
                'relevant' => true,
            ]);

            foreach ($subCategories as $subCategory) {
                Category::create([
                    'name' => $subCategory,
                    'title' => $subCategory,
                    'description' => 'Subcategoria de ' . $parentTitle,
                    'relevant' => true,
                    'parent_id' => $parentCategory->id
                ]);
            }
        }
    }
}
