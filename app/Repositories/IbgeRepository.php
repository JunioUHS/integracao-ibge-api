<?php

namespace App\Repositories;

use App\Services\Rest\HttpService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class IbgeRepository
{
    private HttpService $httpService;
    private string $baseUrl;

    public function __construct(HttpService $httpService)
    {
        $this->httpService = $httpService;
        $this->baseUrl = env('IBGE_API_URL', 'https://servicodados.ibge.gov.br/api');
    }

    public function getCitiesByState(string $uf): array
    {
        try {
            $url = "{$this->baseUrl}/v1/localidades/estados/{$uf}/distritos";
            
            Log::info('Buscando cidades do IBGE', [
                'uf' => $uf,
                'url' => $url
            ]);

            return $this->httpService->get($url);

        } catch (RequestException $e) {
            Log::error('Erro ao buscar cidades do IBGE', [
                'uf' => $uf,
                'status' => $e->response?->status(),
                'message' => $e->getMessage()
            ]);

            throw new \App\Exceptions\IbgeIntegrationException(
                "Erro ao buscar cidades para o estado {$uf}: " . $e->getMessage(),
                previous: $e
            );
        }
    }

    public function getPopulationByYear(int $year, string $locationId): array
    {
        try {
            $url = "{$this->baseUrl}/v3/agregados/6579/periodos/{$year}/variaveis/9324";
            
            Log::info('Buscando população do IBGE', [
                'year' => $year,
                'locationId' => $locationId,
                'url' => $url
            ]);

            return $this->httpService->get($url, [
                'localidades' => "N6[{$locationId}]"
            ]);

        } catch (RequestException $e) {
            Log::error('Erro ao buscar população do IBGE', [
                'year' => $year,
                'locationId' => $locationId,
                'status' => $e->response?->status(),
                'message' => $e->getMessage()
            ]);

            throw new \App\Exceptions\IbgeIntegrationException(
                "Erro ao buscar população para localidade {$locationId} no ano {$year}: " . $e->getMessage(),
                previous: $e
            );
        }
    }
}