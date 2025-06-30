<?php

namespace App\Services\IbgeIntegrator;

use App\Models\Cidade;
use App\Repositories\IbgeRepository;
use App\DTOs\CidadeDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IbgeIntegrationService
{
    private IbgeRepository $ibgeRepository;
    private const CACHE_TTL = 3600; // 1 hora
    private const CACHE_PREFIX_CITIES = 'ibge_cities';
    private const CACHE_PREFIX_POPULATION = 'ibge_population';
    private const DEFAULT_UF = 'N/A';

    public function __construct(IbgeRepository $ibgeRepository)
    {
        $this->ibgeRepository = $ibgeRepository;
    }

    public function getCitiesByState(string $uf): Collection
    {
        $normalizedUf = $this->validateAndNormalizeUf($uf);
        $cacheKey = $this->buildCacheKey(self::CACHE_PREFIX_CITIES, $normalizedUf);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($normalizedUf) {
            try {
                $response = $this->ibgeRepository->getCitiesByState($normalizedUf);
                
                if (empty($response)) {
                    Log::info('Nenhuma cidade encontrada', ['uf' => $normalizedUf]);
                    return collect();
                }
                
                return collect($response)
                    ->filter(fn($city) => $this->isValidCityData($city))
                    ->map(fn($city) => $this->mapCityToDTO($city))
                    ->values(); // Reindexar a collection
                
            } catch (\Exception $e) {
                Log::error('Erro ao buscar cidades do IBGE', [
                    'uf' => $normalizedUf,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                throw new \App\Exceptions\IbgeIntegrationException(
                    "Erro ao buscar cidades para o estado {$normalizedUf}",
                    previous: $e
                );
            }
        });
    }

    public function getPopulationData(int $year, string $locationId): ?int
    {
        $cacheKey = $this->buildCacheKey(self::CACHE_PREFIX_POPULATION, (string)$year, $locationId);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year, $locationId) {
            try {
                $response = $this->ibgeRepository->getPopulationByYear($year, $locationId);
                
                return $response[0]['resultados'][0]['series'][0]['serie'][$year] ?? null;
                
            } catch (\Exception $e) {
                Log::error('Erro ao buscar população do IBGE', [
                    'year' => $year,
                    'locationId' => $locationId,
                    'error' => $e->getMessage()
                ]);
                
                return null;
            }
        });
    }

    private function validateAndNormalizeUf(string $uf): string
    {
        $normalizedUf = strtoupper(trim($uf));
        
        if (strlen($normalizedUf) !== 2) {
            throw new \InvalidArgumentException("UF deve ter exatamente 2 caracteres: {$uf}");
        }
        
        return $normalizedUf;
    }

    private function mapCityToDTO(array $city): CidadeDTO
    {
        return new CidadeDTO(
            id: $city['municipio']['id'],
            nome: $city['municipio']['nome'],
            uf: $this->extractUfFromCity($city)
        );
    }

    private function extractUfFromCity(array $city): string
    {
        $ufPath = $city['municipio']['microrregiao']['mesorregiao']['UF']['sigla'] ?? null;
        
        if ($ufPath) {
            return $ufPath;
        }
        
        Log::warning('UF não encontrada para a cidade', [
            'city' => $city['municipio']['nome'] ?? 'Nome não disponível',
            'city_id' => $city['municipio']['id'] ?? 'ID não disponível'
        ]);
        
        return self::DEFAULT_UF;
    }

    private function isValidCityData(array $city): bool
    {
        return isset($city['municipio']['id'], $city['municipio']['nome']) &&
               !empty($city['municipio']['id']) &&
               !empty($city['municipio']['nome']);
    }

    private function buildCacheKey(string $prefix, string ...$parts): string
    {
        return $prefix . '_' . implode('_', $parts);
    }
}