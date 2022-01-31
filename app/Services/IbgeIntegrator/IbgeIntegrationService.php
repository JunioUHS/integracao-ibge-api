<?php

namespace App\Services\IbgeIntegrator;

use App\Models\Cidade;
use App\Services\IbgeIntegrator\ApiService;
use Illuminate\Support\Arr;

class IbgeIntegrationService extends ApiService
{
    public function getPopulationByYear() {
        $content = $this->_http->get(
            'https://servicodados.ibge.gov.br/api/v3/agregados/6579/periodos/2020|2021/variaveis/9324?localidades=N6[3131307]', 
            [], []
        );

        $result = [];

        if (is_array($content)) {
            $result = $content[0]["resultados"][0]["series"][0]["serie"]["2021"];
        }

        return $result;
    }

    public function getCityByState(string $uf) {
        $content = $this->_http->get(
            'https://servicodados.ibge.gov.br/api/v1/localidades/estados/'. $uf .'/distritos', 
            [], []
        );

        $result = [];

        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $cidade = new Cidade($value["municipio"]["id"], $value["municipio"]["nome"]);
                $result = Arr::add($result, $key, $cidade);
            }
        }

        return $result;
    }
}
