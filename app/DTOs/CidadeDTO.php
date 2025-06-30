<?php

namespace App\DTOs;

class CidadeDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly ?string $uf = null,
        public readonly ?int $populacao = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'uf' => $this->uf,
            'populacao' => $this->populacao,
        ];
    }
}