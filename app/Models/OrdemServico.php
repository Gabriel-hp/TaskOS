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
        'cliente',
        'cnpj',
        'endereco_cliente',
        'contato_cliente',
        'designacao',
        'hora_inicio',
        'hora_fim',
        'modelo_equipamento',
        'numero_equipamento',
        'serial_equipamento',
        'descricao_servico',
        'observacao',
        'materiais',
        'mac',
        'observacoes',
        'protocolo',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
