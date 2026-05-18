<?php

namespace App\Services;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class GHLService
{
    protected $client;
    protected $locationId;
    protected $signupWebhookUrl;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.ghl.url', 'https://services.leadconnectorhq.com/'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.ghl.key'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Version'       => '2021-07-28',
            ],
        ]);

        $this->locationId = config('services.ghl.location_id');
        $this->signupWebhookUrl = config('services.ghl.signup_webhook_url');
    }

    public function createContact($data)
    {
        try {
            $response = $this->client->post('contacts/', [
                'json' => [
                    'firstName'  => $data['firstName'] ?? null,
                    'lastName'   => $data['lastName'] ?? null,
                    'email'      => $data['email'] ?? null,
                    'phone'      => $data['phone'] ?? null,
                    'locationId' => $this->locationId,
                    'tags'       => $data['tags'] ?? [], // ✅ Add tags here
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error'   => true,
                    'message' => $error,
                    'status'  => $e->getResponse()->getStatusCode(),
                ];
            }

            return [
                'error'   => true,
                'message' => $e->getMessage(),
                'status'  => 500,
            ];
        }
    }

    public function sendSignupWebhook(User $user, $signupMethod = 'email')
    {
        if (!$this->signupWebhookUrl) {
            return [
                'error' => true,
                'message' => 'GHL signup webhook URL is not configured.',
                'status' => 0,
            ];
        }

        try {
            $response = $this->client->post($this->signupWebhookUrl, [
                'json' => $this->buildSignupPayload($user, $signupMethod),
                'timeout' => 10,
            ]);

            $body = (string) $response->getBody();
            $decoded = $body ? json_decode($body, true) : null;

            return $decoded ?: [
                'success' => true,
                'status' => $response->getStatusCode(),
                'body' => $body,
            ];
        } catch (RequestException $e) {
            $message = $this->getExceptionMessage($e);

            \Log::warning('GHL Signup Webhook Error', [
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
                'message' => $message,
            ]);

            return [
                'error' => true,
                'message' => $message,
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
            ];
        }
    }

    protected function buildSignupPayload(User $user, $signupMethod)
    {
        list($firstName, $lastName) = $this->splitName($user->name);

        return [
            'email' => $user->email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'event_name' => config('services.ghl.signup_event_name', 'real_signup'),
            'trial_status' => config('services.ghl.signup_trial_status', 'not_started'),
            'signup_method' => $signupMethod,
        ];
    }

    protected function splitName($name)
    {
        $name = trim((string) $name);

        if ($name === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $name, 2);

        return [
            $parts[0],
            isset($parts[1]) ? $parts[1] : '',
        ];
    }

    protected function getExceptionMessage(RequestException $e)
    {
        if ($e->hasResponse()) {
            $body = (string) $e->getResponse()->getBody();
            $decoded = json_decode($body, true);

            return $decoded ?: $body;
        }

        return $e->getMessage();
    }
}
