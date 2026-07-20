<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class AirwallexService
{
    private $client;
    private $baseUrl;
    private $clientId;
    private $apiKey;
    private $accessToken;

    public function __construct(Client $client = null)
    {
        $this->baseUrl = rtrim(config('services.airwallex.base_url'), '/');
        $this->clientId = config('services.airwallex.client_id');
        $this->apiKey = config('services.airwallex.api_key');
        $this->client = $client ?: new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    public function isConfigured()
    {
        return !empty($this->baseUrl) && !empty($this->clientId) && !empty($this->apiKey);
    }

    public function createSalaryTransfer($beneficiaryId, $amount, $reference, $requestId)
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Airwallex is not configured. Add the Client ID and API key to the server environment.');
        }

        if (!$beneficiaryId) {
            throw new RuntimeException('The employee does not have an Airwallex beneficiary ID.');
        }

        $sourceCurrency = strtoupper(config('services.airwallex.source_currency', 'USD'));
        $transferCurrency = strtoupper(config('services.airwallex.transfer_currency', 'PHP'));

        $payload = [
            'beneficiary_id' => $beneficiaryId,
            'transfer_amount' => round((float) $amount, 2),
            'transfer_currency' => $transferCurrency,
            'transfer_method' => 'LOCAL',
            'reason' => 'wages_salary',
            'reference' => substr($reference, 0, 35),
            'request_id' => $requestId,
            'source_currency' => $sourceCurrency,
        ];

        if ($sourceCurrency !== $transferCurrency) {
            $payload['lock_rate_on_create'] = (bool) config('services.airwallex.lock_rate', true);
        }

        return $this->request('POST', '/api/v1/transfers/create', [
            'json' => $payload,
        ]);
    }

    public function getTransfer($transferId)
    {
        return $this->request('GET', '/api/v1/transfers/'.rawurlencode($transferId));
    }

    public function verifyWebhookSignature($timestamp, $signature, $rawBody)
    {
        $secret = (string) config('services.airwallex.webhook_secret');

        if ($secret === '' || !$timestamp || !$signature) {
            return false;
        }

        $timestampMilliseconds = filter_var($timestamp, FILTER_VALIDATE_INT);
        $toleranceMilliseconds = ((int) config('services.airwallex.webhook_tolerance', 300)) * 1000;
        if ($timestampMilliseconds === false || abs((int) round(microtime(true) * 1000) - $timestampMilliseconds) > $toleranceMilliseconds) {
            return false;
        }

        $expected = hash_hmac('sha256', (string) $timestamp.$rawBody, $secret);

        return hash_equals($expected, (string) $signature);
    }

    private function request($method, $path, array $options = [])
    {
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer '.$this->getAccessToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-version' => config('services.airwallex.api_version', '2024-09-27'),
        ]);

        try {
            $response = $this->client->request($method, $this->baseUrl.$path, $options);
            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                throw new RuntimeException('Airwallex returned an invalid response.');
            }

            return $body;
        } catch (RequestException $e) {
            throw new RuntimeException($this->errorMessage($e), 0, $e);
        }
    }

    private function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cacheKey = 'airwallex_access_token_'.sha1($this->clientId.'|'.$this->baseUrl);
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) {
            $this->accessToken = $cachedToken;
            return $this->accessToken;
        }

        try {
            $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'x-client-id' => $this->clientId,
                    'x-api-key' => $this->apiKey,
            ];
            if (config('services.airwallex.account_id')) {
                $headers['x-login-as'] = config('services.airwallex.account_id');
            }

            $response = $this->client->post($this->baseUrl.'/api/v1/authentication/login', [
                'headers' => $headers,
            ]);
            $body = json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            throw new RuntimeException($this->errorMessage($e), 0, $e);
        }

        if (empty($body['token'])) {
            throw new RuntimeException('Airwallex authentication did not return an access token.');
        }

        $this->accessToken = $body['token'];
        Cache::put($cacheKey, $this->accessToken, 25);

        return $this->accessToken;
    }

    private function errorMessage(RequestException $exception)
    {
        $message = 'Airwallex request failed.';

        if ($exception->hasResponse()) {
            $body = json_decode((string) $exception->getResponse()->getBody(), true);
            $message = $body['message'] ?? $body['error']['message'] ?? $message;
        }

        return $message;
    }
}
