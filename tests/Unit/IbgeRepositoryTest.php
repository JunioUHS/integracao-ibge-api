<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\IbgeRepository;
use App\Services\Rest\HttpService;
use App\Exceptions\IbgeIntegrationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class IbgeRepositoryTest extends TestCase
{
    private IbgeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new IbgeRepository(new HttpService());
    }

    public function test_get_cities_by_state_success()
    {
        $mockResponse = [
            [
                'municipio' => [
                    'id' => 123,
                    'nome' => 'Belo Horizonte',
                    'microrregiao' => [
                        'mesorregiao' => [
                            'UF' => ['sigla' => 'MG']
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response($mockResponse, 200)
        ]);

        $result = $this->repository->getCitiesByState('MG');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(123, $result[0]['municipio']['id']);
    }

    public function test_get_cities_by_state_api_error()
    {
        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response(['error' => 'Service unavailable'], 503)
        ]);

        $this->expectException(IbgeIntegrationException::class);
        $this->repository->getCitiesByState('SP');
    }

    public function test_get_population_by_year_success()
    {
        $mockResponse = [
            [
                'resultados' => [
                    [
                        'series' => [
                            [
                                'serie' => [
                                    '2021' => 12396372
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response($mockResponse, 200)
        ]);

        $result = $this->repository->getPopulationByYear(2021, '3550308');

        $this->assertIsArray($result);
        $this->assertEquals(12396372, $result[0]['resultados'][0]['series'][0]['serie']['2021']);
    }
}