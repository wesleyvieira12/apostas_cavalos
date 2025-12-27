<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corrida extends Model
{
    protected $fillable = [
        'nome',
        'data',
        'taxa',
    ];

    protected $casts = [
        'data' => 'datetime',
        'taxa' => 'decimal:2',
    ];

    public function apostas()
    {
        return $this->hasMany(Aposta::class);
    }

    public function apostadores()
    {
        return $this->hasMany(Apostador::class);
    }
}
