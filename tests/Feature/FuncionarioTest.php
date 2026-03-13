<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FuncionarioTest extends TestCase
{
    use RefreshDatabase; // Isso limpa o banco de dados a cada teste

    protected function setUp(): void
    {
        parent::setUp();
        
        // Cria um admin e já deixa logado via Sanctum para todos os testes abaixo
        $admin = Administrador::factory()->create();
        Sanctum::actingAs($admin);
    }

    public function test_deve_ser_possivel_cadastrar_um_funcionario()
    {
        $dados = [
            'nome' => 'Zezinho Teste',
            'login' => 'zezinho.ti',
            'senha' => '123456'
        ];

        $response = $this->postJson('/api/funcionarios', $dados);

        // Verifica se retornou status 201 (Criado)
        $response->assertStatus(201)
                 ->assertJson(['mensagem' => 'Funcionário criado']);

        // Verifica se o caboclo realmente está no banco de dados
        $this->assertDatabaseHas('funcionarios', ['login' => 'zezinho.ti']);
    }

    public function test_nao_deve_permitir_login_duplicado()
    {
        // 1. Cria um funcionário no banco para gerar o conflito
        Funcionario::factory()->create(['login' => 'duplicado']);

        $dados = [
            'nome'  => 'Outro Nome',
            'login' => 'duplicado',
            'senha' => '123456' // <--- AQUI: Precisa ter 6 caracteres ou mais!
        ];

        $response = $this->postJson('/api/funcionarios', $dados);

        // Agora o Laravel vai passar da validação de senha e cair no seu check de login existente
        $response->assertStatus(422)
                ->assertJson(['erro' => 'Login já cadastrado']);
    }
}