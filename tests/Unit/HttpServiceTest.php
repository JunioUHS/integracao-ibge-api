<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Rest\HttpService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class HttpServiceTest extends TestCase
{
    private HttpService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HttpService();
    }

    public function test_get_request_success()
    {
        Http::fake([
            'example.com/*' => Http::response(['data' => 'test'], 200)
        ]);

        $result = $this->service->get('https://example.com/api', ['param' => 'value']);

        $this->assertEquals(['data' => 'test'], $result);
        
        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.com/api?param=value';
        });
    }

    public function test_get_request_with_headers()
    {
        Http::fake([
            'example.com/*' => Http::response(['success' => true], 200)
        ]);

        $headers = ['Authorization' => 'Bearer token'];
        $result = $this->service->get('https://example.com/api', [], $headers);

        $this->assertEquals(['success' => true], $result);
        
        Http::assertSent(function ($request) use ($headers) {
            return $request->hasHeader('Authorization', 'Bearer token');
        });
    }

    public function test_post_request_success()
    {
        Http::fake([
            'example.com/*' => Http::response(['created' => true], 201)
        ]);

        $data = ['name' => 'Test'];
        $result = $this->service->post('https://example.com/api', $data);

        $this->assertEquals(['created' => true], $result);
    }

    public function test_request_failure_throws_exception()
    {
        Http::fake([
            'example.com/*' => Http::response(['error' => 'Not found'], 404)
        ]);

        $this->expectException(RequestException::class);
        $this->service->get('https://example.com/api');
    }
}