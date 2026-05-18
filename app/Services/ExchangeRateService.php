<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class ExchangeRateService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 5]);
    }

    public function getPhpToUsdcRate()
    {
        try {
            return Cache::remember('php_usdc_exchange_rate', 3600, function () {
                return $this->fetchExchangeRate();
            });
        } catch (\Exception $e) {
            Log::error('Failed to get exchange rate', ['error' => $e->getMessage()]);
            return env('DEFAULT_PHP_TO_USDC_RATE', 0.018);
        }
    }

    private function fetchExchangeRate()
    {
        try {
            $response = $this->client->get('https://api.coingecko.com/api/v3/simple/price', [
                'query' => ['ids' => 'usd-coin', 'vs_currencies' => 'php'],
            ]);
            $body = json_decode($response->getBody(), true);
            if (isset($body['usd-coin']['php'])) {
                return $body['usd-coin']['php'];
            }
        } catch (\Exception $e) {
            Log::warning('CoinGecko API failed', ['error' => $e->getMessage()]);
        }

        return 1 / 56;
    }

    public function convertPhpToUsdc($phpAmount)
    {
        return round($phpAmount / $this->getPhpToUsdcRate(), 8);
    }

    public function convertUsdcToPhp($usdcAmount)
    {
        return round($usdcAmount / $this->getPhpToUsdcRate(), 2);
    }

    public function getFormattedRate()
    {
        $rate = $this->getPhpToUsdcRate();
        return [
            'php_per_usdc' => round($rate, 2),
            'usdc_per_php' => round(1 / $rate, 8),
            'display'      => '1 USDC = ' . round($rate, 2) . ' PHP',
            'timestamp'    => now(),
        ];
    }

    public function clearCache()
    {
        Cache::forget('php_usdc_exchange_rate');
    }
}
