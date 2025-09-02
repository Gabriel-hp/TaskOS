<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário administrador padrão
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        // Criar usuário comum para teste
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => Hash::make('123456'),
            'role' => 'user',
        ]);
    }
}
