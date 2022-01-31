<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade
{
    public $id;
    public $nome;

    public function __construct(int $id, string $nome)
    {
        $this->id = $id;
        $this->nome = $nome;
    }
}