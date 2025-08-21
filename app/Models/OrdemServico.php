<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    protected $table = 'ordens_servico';

    protected $fillable = [
        'evento_id',
        'protocolo',
        'cliente_nome',
        'endereco',
        'designacao',
        'motivo',
        'data_hora_inicio',
        'responsavel_id',
        'anexo',
        'observacao',
        'status',
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}
