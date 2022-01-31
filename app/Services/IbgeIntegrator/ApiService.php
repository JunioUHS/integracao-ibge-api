<?php
/**
 * Created by PhpStorm.
 * User: rmncst
 * Date: 31/08/18
 * Time: 09:18
 */

namespace App\Services\IbgeIntegrator;


use App\Services\Rest\RestService;

class ApiService
{
    protected $_apiIntegratorServer;
    // protected $_apiKeyIntegrator;
    protected $_http;

    public function __construct(RestService $http) {
        $this->_apiIntegratorServer = env('API_IBGE_SERVER');
        // $this->_apiKeyIntegrator = env('INTEGRATOR_KEY');
        $this->_http = $http;
    }


}
