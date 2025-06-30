<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cidade extends Model
{
    use HasFactory;

    protected $table = 'cidades';
    
    protected $fillable = [
        'ibge_id',
        'nome',
        'estado_uf',
        'populacao',
        'updated_at'
    ];

    protected $casts = [
        'ibge_id' => 'integer',
        'populacao' => 'integer',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_uf', 'uf');
    }
}