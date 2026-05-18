<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SolanaTokenService
{
    private $rpcUrl;
    private $usdcMint;
    private $client;

    public function __construct()
    {
        $this->rpcUrl = env('SOLANA_RPC_URL', 'https://api.mainnet-beta.solana.com');
        $this->usdcMint = env('USDC_MINT_ADDRESS', 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v');

        // Initialize Guzzle Client
        $this->client = new Client([
            'timeout' => 30,
        ]);
    }

    /**
     * Get transaction details
     */
    public function getTransaction($signature)
    {
        try {
            $response = $this->client->post($this->rpcUrl, [
                'json' => [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'getTransaction',
                    'params' => [
                        $signature,
                        'json'
                    ]
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            return $body;

        } catch (\Exception $e) {
            Log::error('Error fetching transaction', [
                'error' => $e->getMessage(),
                'signature' => $signature,
            ]);
            return null;
        }
    }

    /**
     * Make RPC call
     */
    public function makeRpcCall($method, $params = [])
    {
        try {
            $response = $this->client->post($this->rpcUrl, [
                'json' => [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => $method,
                    'params' => $params
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            return $body;

        } catch (\Exception $e) {
            Log::error('RPC call failed', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Example: Get Token Accounts (NOW IMPLEMENTED)
     */
    public function getTokenAccounts($walletAddress)
    {
        try {
            $result = $this->makeRpcCall('getTokenAccountsByOwner', [
                $walletAddress,
                [
                    'programId' => 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA'
                ],
                [
                    'encoding' => 'jsonParsed'
                ]
            ]);

            if (isset($result['result']['value'])) {
                return array_map(function ($item) {
                    return [
                        'pubkey' => $item['pubkey'],
                        'mint' => $item['account']['data']['parsed']['info']['mint'],
                        'amount' => $item['account']['data']['parsed']['info']['tokenAmount']['uiAmount'],
                    ];
                }, $result['result']['value']);
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Error fetching token accounts', [
                'error' => $e->getMessage(),
                'wallet' => $walletAddress,
            ]);
            return [];
        }
    }

    /**
     * Get USDC balance (NOW WORKING)
     */
    public function getUsdcBalance($walletAddress)
    {
        try {
            $accounts = $this->getTokenAccounts($walletAddress);

            $balance = 0;

            foreach ($accounts as $account) {
                if ($account['mint'] === $this->usdcMint) {
                    $balance += $account['amount'];
                }
            }

            return $balance;

        } catch (\Exception $e) {
            Log::error('Error fetching USDC balance', [
                'error' => $e->getMessage(),
                'wallet' => $walletAddress,
            ]);
            throw $e;
        }
    }
}