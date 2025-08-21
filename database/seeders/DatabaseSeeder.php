<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Evento;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Criar usuários de exemplo
        User::create([
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'email' => 'joao.silva@taskos.com',
            'username' => 'joao.silva',
            'password' => '123456',
        ]);

        User::create([
            'nome' => 'Maria',
            'sobrenome' => 'Santos',
            'email' => 'maria.santos@taskos.com',
            'username' => 'maria.santos',
            'password' => '123456',
        ]);

        User::create([
            'nome' => 'Pedro',
            'sobrenome' => 'Oliveira',
            'email' => 'pedro.oliveira@taskos.com',
            'username' => 'pedro.oliveira',
            'password' => '123456',
        ]);

        // Criar alguns eventos de exemplo
        Evento::create([
            'titulo' => 'Manutenção Preventiva',
            'assunto' => 'Verificação de equipamentos de rede',
            'endereco' => 'Rua das Flores, 123 - Centro - São Paulo/SP',
            'data_hora' => now()->addDays(1)->setTime(9, 0),
            'responsavel_id' => 1,
            'status' => 'Pendente',
        ]);

        Evento::create([
            'titulo' => 'Instalação de Sistema',
            'assunto' => 'Instalação de novo sistema de monitoramento',
            'endereco' => 'Av. Principal, 456 - Jardim América - São Paulo/SP',
            'data_hora' => now()->addDays(2)->setTime(14, 0),
            'responsavel_id' => 2,
            'status' => 'Pendente',
        ]);

        Evento::create([
            'titulo' => 'Suporte Técnico',
            'assunto' => 'Resolução de problemas de conectividade',
            'endereco' => 'Rua Industrial, 789 - Distrito Industrial - São Paulo/SP',
            'data_hora' => now()->addDays(3)->setTime(10, 30),
            'responsavel_id' => 3,
            'status' => 'Pendente',
        ]);
    }
}
