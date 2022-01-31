<?php

namespace App\Services\Rest;


use Curl\Curl;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RestService
{
    public function get($url , array $queryParameters = [], array $headers = []) {
        $curl = new Curl();

        foreach ($headers as $k => $v) {
            $curl->setHeader($k , $v);
        }

        $curl->get($url, $queryParameters);
        if($curl->error) {
            return $curl->getHttpStatusCode();
        }

        return $this->convertResponseToArray($curl->response);
    }

    public function post($url,array $parameters = [], array $queryParameters = [], array $headers = []) {

        $curl = new Curl();

        foreach ($headers as $k => $v) {
            $curl->setHeader($k , $v);
        }
        if(count($queryParameters) > 0) {
            $url .= '?';
        }
        foreach ($queryParameters as $k => $v) {
            $url .= $k.'='.$v.'&';
        }

        $curl->post($url,$parameters);
        if($curl->error) {
            throw new HttpException($curl->getHttpStatusCode(), $curl->getErrorMessage());
        }

        return $this->convertResponseToArray($curl->response);
    }

    public function put($url,array $parameters = [], array $queryParameters = [], array $headers = []) {
        $curl = new Curl();

        foreach ($headers as $k => $v) {
            $curl->setHeader($k , $v);
        }
        if(count($queryParameters) > 0) {
            $url .= '?';
        }
        foreach ($queryParameters as $k => $v) {
            $url .= $k.'='.$v.'&';
        }

        $curl->put($url,$parameters);
        if($curl->error) {
            throw new HttpException($curl->getHttpStatusCode(), $curl->getErrorMessage());
        }

        return $this->convertResponseToArray($curl->response);
    }

    public function delete($url,array $parameters = [], array $queryParameters = [], array $headers = []) {
        $curl = new Curl();

        foreach ($headers as $k => $v) {
            $curl->setHeader($k , $v);
        }
        $curl->delete($url,$queryParameters, $parameters);
        if($curl->error) {
            throw new HttpException($curl->getHttpStatusCode(), $curl->getErrorMessage());
        }

        return $this->convertResponseToArray($curl->response);
    }

    public function convertResponseToArray($item) {
        return json_decode(json_encode($item),true);
    }

}
