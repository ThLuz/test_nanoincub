<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\FuncionarioRequest;
use Illuminate\Support\Facades\DB;

class FuncionarioController extends Controller
{
    public function index()
    {
        //$funcionarios = DB::select("SELECT id, nome, login, saldo FROM funcionarios WHERE deleted = 0");
        $funcionarios = Funcionario::where('deleted', 0)->select('id', 'nome', 'login', 'saldo')->get();

        return response()->json($funcionarios);
    }

    public function show($id)
    {
        //$funcionario = DB::select("SELECT id, nome, login, saldo FROM funcionarios WHERE id = $id AND deleted = 0");
        $funcionario = Funcionario::where('id', $id)->where('deleted', 0)->select('id', 'nome', 'login', 'saldo')->first();        

        if (!$funcionario) {
            return response()->json(['erro' => 'Funcionário não encontrado'], 404);
        }

        return response()->json($funcionario);
    }

    public function store(FuncionarioRequest $request)
    {
        $validated = $request->validated();
        $nome  = $validated['nome'];
        $login = $validated['login'];
        $senha = $validated['senha'];

        //$existe = DB::select("SELECT id FROM funcionarios WHERE login = '$login' AND deleted = 0");
        $existe = Funcionario::where('login', $login)->where('deleted', 0)->select('id')->exists(); 
        if ($existe) {
            return response()->json(['erro' => 'Login já cadastrado'], 422);
        }

        //DB::insert("INSERT INTO funcionarios (nome, login, senha, saldo, deleted) VALUES ('$nome', '$login', '$senha', 0, 0)");

        //$id = DB::getPdo()->lastInsertId();
        $funcionario = Funcionario::create(['nome' => $nome, 'login' => $login, 'senha' => Hash::make($senha), 'saldo' => 0, 'deleted' => 0]);

        return response()->json(['mensagem' => 'Funcionário criado', 'id' => $funcionario->id], 201);
    }

    public function update(FuncionarioRequest $request, $id)
    {
        $validated = $request->validated();
        $nome  = $validated['nome'];
        $login = $validated['login'];

        //$existe = DB::select("SELECT id FROM funcionarios WHERE login = '$login' AND id != $id AND deleted = 0");
        $existe = Funcionario::where('login', $login)->where('id', '!=', $id)->where('deleted', 0)->exists();        
        if ($existe) {
            return response()->json(['erro' => 'Login já cadastrado'], 422);
        }

        //DB::update("UPDATE funcionarios SET nome = '$nome', login = '$login' WHERE id = $id");
        Funcionario::where('id', $id)->update(['nome' => $nome, 'login' => $login]);      

        return response()->json(['mensagem' => 'Funcionário atualizado']);
    }

    public function destroy($id)
    {
        //DB::update("UPDATE funcionarios SET deleted = 1 WHERE id = $id");
        Funcionario::where('id', $id)->update(['deleted' => 1]);

        return response()->json(['mensagem' => 'Funcionário removido']);
    }
}
