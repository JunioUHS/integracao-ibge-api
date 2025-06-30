<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IbgeIntegrationTest extends TestCase
{
    public function test_get_cities_by_state_success()
    {
        Http::fake([
            'servicodados.ibge.gov.br/*' => Http::response([
                ['municipio' => ['id' => 123, 'nome' => 'Belo Horizonte']]
            ], 200)
        ]);

        $response = $this->getJson('/api/v1/ibge/cidades?uf=MG');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'body' => [
                        '*' => ['id', 'nome', 'uf']
                    ]
                ]);
    }

    public function test_get_cities_invalid_uf()
    {
        $response = $this->getJson('/api/v1/ibge/cidades?uf=XX');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['uf']);
    }
}