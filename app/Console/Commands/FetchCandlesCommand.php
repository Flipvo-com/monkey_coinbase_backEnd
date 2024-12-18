<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCandlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-candles {productId=BTC-USD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch candles for a specific product from Coinbase API and log the response';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $productId = $this->argument('productId');

        $startTime = time() - (60 * 60 * 24 * 7); // 7 days ago in UNIX timestamp
        $endTime = time(); // Current time in UNIX timestamp
        $granularity = 'ONE_HOUR';
        $limit = 350;

        try {
            $url = "https://api.coinbase.com/api/v3/brokerage/market/products/{$productId}/candles";

            $response = Http::get($url, [
                'start' => $startTime,
                'end' => $endTime,
                'granularity' => $granularity,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                $candles = $response->json();
                Log::info('Candles Data:', $candles);
                $this->info('Candles fetched successfully!');
            } else {
                Log::error('Failed to fetch candles', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                $this->error('Failed to fetch candle data.');
            }
        } catch (\Exception $e) {
            Log::error('Error fetching candles:', ['message' => $e->getMessage()]);
            $this->error('An error occurred while fetching candles.');
        }
    }
}
