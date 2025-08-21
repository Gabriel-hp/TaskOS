<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'assunto',
        'endereco',
        'data_hora',
        'responsavel_id',
        'status',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function ordemServico()
    {
        return $this->hasOne(OrdemServico::class);
    }
}
