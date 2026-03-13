<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdministradorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'  => $this->faker->name(), // <--- ADICIONE ESTA LINHA
            'login' => $this->faker->unique()->userName(),
            'senha' => bcrypt('admin123'),
        ];
    }
}