<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MovimentacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Já deixa um admin logado para todos os testes
        Sanctum::actingAs(Administrador::factory()->create());
    }

    public function test_deve_somar_saldo_ao_registrar_entrada()
    {
        // 1. Prepara: Funcionário com saldo 100
        $funcionario = Funcionario::factory()->create(['saldo' => 100, 'deleted' => 0]);

        // 2. Age: Registra entrada de 50
        $response = $this->postJson("/api/funcionarios/{$funcionario->id}/movimentacoes", [
            'tipo' => 'entrada',
            'valor' => 50,
            'descricao' => 'Depósito via Teste'
        ]);

        // 3. Verifica
        $response->assertStatus(200);
        $this->assertEquals(150, $funcionario->fresh()->saldo);
        $this->assertDatabaseHas('movimentacoes', [
            'funcionario_id' => $funcionario->id,
            'tipo' => 'entrada',
            'valor' => 50
        ]);
    }

    public function test_deve_subtrair_saldo_ao_registrar_saida()
    {
        // 1. Prepara: Funcionário com saldo 100
        $funcionario = Funcionario::factory()->create(['saldo' => 100, 'deleted' => 0]);

        // 2. Age: Registra saída de 40
        $response = $this->postJson("/api/funcionarios/{$funcionario->id}/movimentacoes", [
            'tipo' => 'saida',
            'valor' => 40,
            'descricao' => 'Saque via Teste'
        ]);

        // 3. Verifica
        $response->assertStatus(200);
        $this->assertEquals(60, $funcionario->fresh()->saldo);
    }

    public function test_nao_deve_permitir_saida_se_saldo_for_insuficiente()
    {
        // 1. Prepara: Saldo baixo
        $funcionario = Funcionario::factory()->create(['saldo' => 10, 'deleted' => 0]);

        // 2. Age: Tenta tirar mais do que tem
        $response = $this->postJson("/api/funcionarios/{$funcionario->id}/movimentacoes", [
            'tipo' => 'saida',
            'valor' => 100,
            'descricao' => 'Tentativa sem saldo'
        ]);

        // 3. Verifica: O status 422 que você definiu no abort() do controller
        $response->assertStatus(422);
        
        // O saldo não pode ter mudado
        $this->assertEquals(10, $funcionario->fresh()->saldo);
    }

    public function test_deve_listar_movimentacoes_do_funcionario()
    {
        $funcionario = Funcionario::factory()->create();
        
        // Cria 3 movimentações usando a Factory (se você a criou)
        // Ou via Eloquent simples:
        Movimentacao::create([
            'funcionario_id' => $funcionario->id,
            'tipo' => 'entrada',
            'valor' => 10,
            'descricao' => 'teste 1',
            'created_at' => now()
        ]);

        $response = $this->getJson("/api/funcionarios/{$funcionario->id}/movimentacoes");

        $response->assertStatus(200)->assertJsonCount(1);
    }
}