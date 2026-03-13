<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimentacao;
use App\Models\Funcionario;
use App\Http\Requests\MovimentacaoRequest;
use Illuminate\Support\Facades\DB;

class MovimentacaoController extends Controller
{
    public function index($funcionarioId)
    {
        //$movimentacoes = DB::select("SELECT * FROM movimentacoes WHERE funcionario_id = $funcionarioId ORDER BY created_at DESC");
        $movimentacoes = Movimentacao::where('funcionario_id', $funcionarioId)->orderBy('created_at', 'DESC')->get();

        return response()->json($movimentacoes);
    }

    public function store(MovimentacaoRequest  $request, $funcionarioId)
    {
        $validated = $request->validated();
        $tipo  = $validated['tipo'];
        $valor = $validated['valor'];
        $descricao = $validated['descricao'];

        //$funcionario = DB::select("SELECT * FROM funcionarios WHERE id = $funcionarioId AND deleted = 0");
        $funcionario = Funcionario::where('id', $funcionarioId)->where('deleted', 0)->lockForUpdate()->first();

        if (empty($funcionario)) {
            return response()->json(['erro' => 'Funcionário não encontrado'], 404);
        }

        //$funcionario = $funcionario[0];
        $saldoAtual  = $funcionario->saldo;

        $resultado = DB::transaction(function () use ($funcionario, $tipo, $valor, $descricao, $saldoAtual, $funcionarioId) {
            if ($tipo === 'saida') {
                if ($saldoAtual < $valor) {
                    //return response()->json(['erro' => 'Saldo insuficiente'], 422);
                    abort(422, 'Saldo insuficiente');
                }

                //DB::insert("INSERT INTO movimentacoes (funcionario_id, tipo, valor, descricao, created_at) VALUES ($funcionarioId, 'saida', $valor, '$descricao', NOW())");
                Movimentacao::create(['funcionario_id' => $funcionarioId, 'tipo' => 'saida', 'valor' => $valor, 'descricao' => $descricao, 'created_at' => now()]);

                //$novoSaldo = $saldoAtual - $valor;
                //DB::update("UPDATE funcionarios SET saldo = $novoSaldo WHERE id = $funcionarioId");

                $funcionario->decrement('saldo', $valor);

                //return response()->json(['mensagem' => 'Saída registrada', 'saldo' => $funcionario->fresh()->saldo]);
                return ['mensagem' => 'Saída registrada', 'saldo' => $funcionario->fresh()->saldo];

            }

            if ($tipo === 'entrada') {
                //DB::insert("INSERT INTO movimentacoes (funcionario_id, tipo, valor, descricao, created_at) VALUES ($funcionarioId, 'entrada', $valor, '$descricao', NOW())");
                Movimentacao::create(['funcionario_id' => $funcionarioId, 'tipo' => 'entrada', 'valor' => $valor, 'descricao' => $descricao, 'created_at' => now()]);

                //$novoSaldo = $saldoAtual + $valor;
                //DB::update("UPDATE funcionarios SET saldo = $novoSaldo WHERE id = $funcionarioId");
                $funcionario->increment('saldo', $valor);

                //return response()->json(['mensagem' => 'Entrada registrada', 'saldo' => $funcionario->fresh()->saldo]);
                return ['mensagem' => 'Entrada registrada', 'saldo' => $funcionario->fresh()->saldo];

            }

            //return response()->json(['erro' => 'Tipo inválido. Use "entrada" ou "saida"'], 422);
            abort(422, 'Tipo inválido. Use "entrada" ou "saida"');
        });

        return response()->json($resultado);    
    }
}
