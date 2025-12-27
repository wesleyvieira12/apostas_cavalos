<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aposta extends Model
{
    protected $fillable = [
        'apostador_id',
        'corrida_id',
        'rodada',
        'animal',
        'valor',
        'lo',
    ];

    protected $casts = [
        'lo' => 'decimal:2',
        'valor' => 'decimal:2',
    ];

    public function apostador()
    {
        return $this->belongsTo(Apostador::class);
    }

    public function corrida()
    {
        return $this->belongsTo(Corrida::class);
    }
}
