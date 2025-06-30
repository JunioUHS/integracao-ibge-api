<?php

namespace App\Repositories;

use App\Services\Rest\RestService;

class IbgeRepository
{
    private RestService $restService;
    private string $baseUrl;

    public function __construct(RestService $restService)
    {
        $this->restService = $restService;
        $this->baseUrl = env('API_IBGE_SERVER', 'https://servicodados.ibge.gov.br/api');
    }

    public function getCitiesByState(string $uf): array
    {
        $url = "{$this->baseUrl}/v1/localidades/estados/{$uf}/distritos";
        return $this->restService->get($url);
    }

    public function getPopulationByYear(int $year, string $locationId): array
    {
        $url = "{$this->baseUrl}/v3/agregados/6579/periodos/{$year}/variaveis/9324";
        return $this->restService->get($url, [
            'localidades' => "N6[{$locationId}]"
        ]);
    }
}