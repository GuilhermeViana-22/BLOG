<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Lista de categorias e subcategorias
        $categories = [
            'Linguagens de Programação' => ['Elixir', 'Go', 'Pascal', 'Python', 'JavaScript', 'Java', 'C++', 'Ruby'],
            'Desenvolvimento Web' => ['HTML', 'CSS', 'PHP', 'Laravel', 'React', 'Vue.js', 'Angular'],
            'Banco de Dados' => ['MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis'],
            'DevOps e Infraestrutura' => ['Docker', 'Kubernetes', 'Ansible', 'Terraform', 'AWS', 'Azure'],
            'Inteligência Artificial e Machine Learning' => ['TensorFlow', 'PyTorch', 'Keras', 'Scikit-learn', 'OpenCV'],
            'Tecnologias Emergentes' => ['Blockchain', 'Quantum Computing', 'Internet das Coisas (IoT)', 'Realidade Aumentada (AR)', 'Realidade Virtual (VR)']
        ];

        // Escolhe uma categoria principal ou uma subcategoria
        $parentCategory = $this->faker->randomElement(array_keys($categories));
        $title = $this->faker->randomElement($categories[$parentCategory]);

        return [
            'name' => $title,
            'title' => $title,
            'description' => $this->faker->sentence,
            'relevant' => $this->faker->boolean,
            'parent_id' => $parentCategory === 'Linguagens de Programação' ? null : \App\Models\Category::where('name', $parentCategory)->first()->id
        ];
    }
}
