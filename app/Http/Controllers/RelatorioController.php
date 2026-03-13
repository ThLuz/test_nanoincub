<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        //$funcionarios = DB::select("SELECT * FROM funcionarios WHERE deleted = 0");
        $funcionarios = Funcionario::with('movimentacoes')->where('deleted', 0)->get();
        $resultado = [];

        foreach ($funcionarios as $f) {
            //$movimentacoes = DB::select("SELECT * FROM movimentacoes WHERE funcionario_id = {$f->id}");
            $movimentacoes = $f->movimentacoes;
            $totalEntradas = 0;
            $totalSaidas = 0;
            foreach ($movimentacoes as $m) {
                if ($m->tipo === 'entrada') {
                    $totalEntradas += $m->valor;
                } else {
                    $totalSaidas += $m->valor;
                }
            }
            $resultado[] = [
                'id' => $f->id,
                'nome' => $f->nome,
                'saldo' => $f->saldo,
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'movimentacoes_count' => count($movimentacoes),
            ];
        }

        return response()->json($resultado);
    }
}
