<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_pode_fazer_login()
    {
        $admin = Administrador::factory()->create([
            'senha' => bcrypt('admin123')
        ]);

        $response = $this->postJson('/api/login', [
            'login' => $admin->login,
            'senha' => 'admin123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_funcionario_nao_pode_fazer_login()
    {
        $funcionario = Funcionario::factory()->create([
            'senha' => bcrypt('senha123')
        ]);

        $response = $this->postJson('/api/login', [
            'login' => $funcionario->login,
            'senha' => 'senha123'
        ]);

        // Aqui esperamos 401, pois funcionário não tem acesso
        $response->assertStatus(401);
    }

    public function test_logout_revoga_token_do_admin()
    {
        $admin = Administrador::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $admin->fresh()->tokens);
    }
}