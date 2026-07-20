<?php

namespace Tests\Unit;

use App\Services\AirwallexService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AirwallexServiceTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.airwallex.base_url' => 'https://api-demo.airwallex.com',
            'services.airwallex.client_id' => 'test-client',
            'services.airwallex.api_key' => 'test-key',
            'services.airwallex.webhook_secret' => 'test-webhook-secret',
            'services.airwallex.source_currency' => 'USD',
            'services.airwallex.transfer_currency' => 'PHP',
            'services.airwallex.lock_rate' => true,
            'services.airwallex.api_version' => '2024-09-27',
            'services.airwallex.webhook_tolerance' => 300,
        ]);

        Cache::flush();
    }

    public function test_it_authenticates_and_creates_a_salary_transfer()
    {
        $history = [];
        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json'], json_encode([
                'token' => 'sandbox-token',
                'expires_at' => '2099-01-01T00:00:00+0000',
            ])),
            new Response(201, ['Content-Type' => 'application/json'], json_encode([
                'id' => 'transfer_123',
                'status' => 'SCHEDULED',
            ])),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        $client = new Client(['handler' => $stack]);
        $service = new AirwallexService($client);

        $result = $service->createSalaryTransfer('beneficiary_123', 16875, 'Salary 20260601-20260615', 'request_123');

        $this->assertSame('transfer_123', $result['id']);
        $this->assertCount(2, $history);
        $this->assertSame('test-client', $history[0]['request']->getHeaderLine('x-client-id'));
        $this->assertSame('test-key', $history[0]['request']->getHeaderLine('x-api-key'));
        $this->assertSame('Bearer sandbox-token', $history[1]['request']->getHeaderLine('Authorization'));

        $payload = json_decode((string) $history[1]['request']->getBody(), true);
        $this->assertSame('beneficiary_123', $payload['beneficiary_id']);
        $this->assertEquals(16875.0, $payload['transfer_amount']);
        $this->assertSame('PHP', $payload['transfer_currency']);
        $this->assertSame('USD', $payload['source_currency']);
        $this->assertSame('wages_salary', $payload['reason']);
        $this->assertTrue($payload['lock_rate_on_create']);
    }

    public function test_it_verifies_webhook_signatures()
    {
        $service = new AirwallexService(new Client());
        $timestamp = (string) ((int) round(microtime(true) * 1000));
        $body = '{"name":"payout.transfer.completed"}';
        $signature = hash_hmac('sha256', $timestamp.$body, 'test-webhook-secret');

        $this->assertTrue($service->verifyWebhookSignature($timestamp, $signature, $body));
        $this->assertFalse($service->verifyWebhookSignature($timestamp, 'invalid', $body));
    }
}
