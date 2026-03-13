<?php

namespace Database\Factories;

use App\Models\Movimentacao;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovimentacaoFactory extends Factory
{
    /**
     * O nome do model correspondente à factory.
     *
     * @var string
     */
    protected $model = Movimentacao::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'funcionario_id' => Funcionario::factory(),
            'tipo'           => $this->faker->randomElement(['entrada', 'saida']),
            'valor'          => $this->faker->randomFloat(2, 10, 500),
            'descricao'      => $this->faker->sentence(3),
            'created_at'     => now(),
        ];
    }
}