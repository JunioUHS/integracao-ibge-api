<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetCitiesRequest;
use App\Http\Resources\CidadeResource;
use App\Services\IbgeIntegrator\IbgeIntegrationService;
use Illuminate\Http\JsonResponse;

class IntegracaoIbgeController extends Controller
{
    public function __construct(
        private IbgeIntegrationService $ibgeService
    ) {}

    public function getCitiesByState(GetCitiesRequest $request): JsonResponse
    {
        $cities = $this->ibgeService->getCitiesByState($request->uf);
        
        return response()->jsonResponse(
            data: CidadeResource::collection($cities),
            status: 200,
            message: 'Cidades encontradas com sucesso'
        );
    }

    public function getPopulation(string $locationId, int $year): JsonResponse
    {
        $population = $this->ibgeService->getPopulationData($year, $locationId);
        
        return response()->jsonResponse(
            data: ['populacao' => $population],
            status: 200,
            message: $population ? 'População encontrada' : 'Dados não disponíveis'
        );
    }
}