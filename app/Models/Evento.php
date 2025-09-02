<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'assunto',
        'endereco',
        'start',
        'responsavel_id',
        'status',
    ];

    protected $casts = [
        'start' => 'datetime',
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function ordemServico()
    {
        return $this->hasOne(OrdemServico::class, 'evento_id');
    }
}
