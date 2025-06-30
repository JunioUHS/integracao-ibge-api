<?php

namespace App\Services\Rest;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class HttpService
{
    private int $timeout;
    private int $retries;

    public function __construct()
    {
        $this->timeout = (int) env('IBGE_TIMEOUT', 30);
        $this->retries = (int) env('IBGE_RETRIES', 2);
    }

    public function get(string $url, array $queryParameters = [], array $headers = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 1000)
                ->withHeaders($headers)
                ->get($url, $queryParameters);

            $this->ensureSuccessful($response);

            return $response->json() ?? [];

        } catch (RequestException $e) {
            $this->logError('GET', $url, $e, $queryParameters);
            throw $e;
        }
    }

    public function post(string $url, array $parameters = [], array $queryParameters = [], array $headers = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 1000)
                ->withHeaders($headers)
                ->post($url . $this->buildQueryString($queryParameters), $parameters);

            $this->ensureSuccessful($response);

            return $response->json() ?? [];

        } catch (RequestException $e) {
            $this->logError('POST', $url, $e, $parameters);
            throw $e;
        }
    }

    public function put(string $url, array $parameters = [], array $queryParameters = [], array $headers = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 1000)
                ->withHeaders($headers)
                ->put($url . $this->buildQueryString($queryParameters), $parameters);

            $this->ensureSuccessful($response);

            return $response->json() ?? [];

        } catch (RequestException $e) {
            $this->logError('PUT', $url, $e, $parameters);
            throw $e;
        }
    }

    public function delete(string $url, array $parameters = [], array $queryParameters = [], array $headers = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 1000)
                ->withHeaders($headers)
                ->delete($url . $this->buildQueryString($queryParameters), $parameters);

            $this->ensureSuccessful($response);

            return $response->json() ?? [];

        } catch (RequestException $e) {
            $this->logError('DELETE', $url, $e, $parameters);
            throw $e;
        }
    }

    private function ensureSuccessful(Response $response): void
    {
        if (!$response->successful()) {
            throw new RequestException($response);
        }
    }

    private function buildQueryString(array $queryParameters): string
    {
        if (empty($queryParameters)) {
            return '';
        }

        return '?' . http_build_query($queryParameters);
    }

    private function logError(string $method, string $url, RequestException $e, array $data = []): void
    {
        Log::error("HTTP {$method} request failed", [
            'url' => $url,
            'status' => $e->response?->status(),
            'message' => $e->getMessage(),
            'data' => $data,
            'response_body' => $e->response?->body()
        ]);
    }
}
