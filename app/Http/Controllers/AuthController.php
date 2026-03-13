<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrador;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $login = $validated['login'];
        $senha = $validated['senha'];
        //$login = $request->login;
        //$senha = $request->senha;

        //$admin = DB::select("SELECT * FROM administradores WHERE login = '$login' AND senha = '$senha'");
        //if (empty($admin)) {
           //return response()->json(['erro' => 'Credenciais inválidas'], 401);
        //}
        //$admin = $admin[0];

        $admin = Administrador::where('login', $login)->first();

        if (!$admin || !Hash::check($senha, $admin->senha)) {
            return response()->json(['erro' => 'Credenciais inválidas'], 401);
        }        

        //$token = rand(100000, 999999);
        //DB::update("UPDATE administradores SET token = '$token' WHERE id = $admin->id");

        $adminModel = Administrador::find($admin->id);
        $token = $adminModel->createToken('api-token')->plainTextToken;        

        return response()->json([
            'mensagem' => 'Login realizado com sucesso',
            'token'    => $token,
            'admin'    => [
                'id'    => $admin->id,
                'nome'  => $admin->nome,
                'login' => $admin->login,
                //'senha' => $admin->senha,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        //$token = $request->header('Authorization');

        //DB::update("UPDATE administradores SET token = NULL WHERE token = '$token'");
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensagem' => 'Logout realizado']);
    }
}
