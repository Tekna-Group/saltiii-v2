<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class GHLService
{
    protected $client;
    protected $locationId;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('GHL_API_URL', 'https://services.leadconnectorhq.com/'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('GHL_API_KEY'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Version'       => '2021-07-28',
            ],
        ]);

        $this->locationId = env('GHL_LOCATION_ID');
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
}
