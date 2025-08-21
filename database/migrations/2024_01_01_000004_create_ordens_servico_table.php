<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos');
            $table->string('protocolo')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->string('cliente_nome')->nullable(); // Para texto livre
            $table->text('endereco');
            $table->text('designacao');
            $table->text('motivo');
            $table->dateTime('data_hora_inicio');
            $table->foreignId('responsavel_id')->constrained('users');
            $table->string('anexo')->nullable();
            $table->text('observacao')->nullable();
            $table->enum('status', ['Pendente', 'Em execução', 'Finalizado'])->default('Pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ordens_servico');
    }
};
