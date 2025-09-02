<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('assunto');
            $table->string('endereco');
            $table->datetime('start');
            $table->foreignId('responsavel_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['agendado', 'feito', 'reagendado', 'cancelado'])->default('agendado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
