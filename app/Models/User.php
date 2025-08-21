<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sobrenome',
        'email',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'responsavel_id');
    }

    public function ordensServico()
    {
        return $this->hasMany(OrdemServico::class, 'responsavel_id');
    }

    public function getNomeCompletoAttribute()
    {
        return $this->nome . ' ' . $this->sobrenome;
    }

    public function getUsernameAttribute($value)
    {
        return $value ?: strtolower($this->nome . '.' . $this->sobrenome);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
