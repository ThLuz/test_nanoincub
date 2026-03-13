<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Funcionario>
 */
class FuncionarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'nome' => $this->faker->name,
            'login' => $this->faker->unique()->userName,
            'senha' => bcrypt('123456'),
            'saldo' => 0,
            'deleted' => 0,
        ];
    }
}
