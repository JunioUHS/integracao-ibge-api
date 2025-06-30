<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class IbgeIntegrationException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->jsonResponse(
            data: [],
            status: 503,
            message: $this->getMessage() ?: 'Erro na integração com IBGE'
        );
    }
}