<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apostador extends Model
{
    protected $fillable = [
        'nome',
        'corrida_id',
    ];
    protected $table = 'apostadores';

    public function apostas()
    {
        return $this->hasMany(Aposta::class);
    }

    public function corrida()
    {
        return $this->belongsTo(Corrida::class);
    }
}
