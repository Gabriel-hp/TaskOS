<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            
            // Dados da empresa/cliente
            $table->string('cliente')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('endereco_cliente')->nullable();
            $table->string('contato_cliente')->nullable();
            $table->string('designacao')->nullable();
            
            // Horários
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            
            // Dados do equipamento
            $table->string('modelo_equipamento')->nullable();
            $table->string('numero_equipamento')->nullable();
            $table->string('serial_equipamento')->nullable();
            
            // Serviço
            $table->text('descricao_servico')->nullable();
            $table->text('observacao')->nullable();
            
            // Campos originais mantidos
            $table->text('materiais')->nullable();
            $table->string('mac')->nullable();
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
    }
};
