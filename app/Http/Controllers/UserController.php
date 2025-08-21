<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|string|min:6',
        ]);

        $username = strtolower($request->nome . '.' . $request->sobrenome);

        // Verificar se username já existe
        $counter = 1;
        $originalUsername = $username;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'nome' => $request->nome,
            'sobrenome' => $request->sobrenome,
            'email' => $request->email,
            'username' => $username,
            'password' => $request->senha,
        ]);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'sobrenome' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'senha' => 'sometimes|nullable|string|min:6',
        ]);

        $data = $request->only(['nome', 'sobrenome', 'email']);

        if ($request->filled('senha')) {
            $data['password'] = $request->senha;
        }

        // Atualizar username se nome ou sobrenome mudaram
        if ($request->has('nome') || $request->has('sobrenome')) {
            $nome = $request->nome ?? $user->nome;
            $sobrenome = $request->sobrenome ?? $user->sobrenome;
            $username = strtolower($nome . '.' . $sobrenome);

            // Verificar se username já existe (exceto para o usuário atual)
            $counter = 1;
            $originalUsername = $username;
            while (User::where('username', $username)->where('id', '!=', $id)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            $data['username'] = $username;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar se o usuário tem eventos ou O.S. associadas
        if ($user->eventos()->count() > 0 || $user->ordensServico()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir usuário com eventos ou ordens de serviço associadas.'
            ], 422);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
