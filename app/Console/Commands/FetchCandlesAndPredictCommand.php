<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Client as OpenAIClient;

class FetchCandlesAndPredictCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-candles-predict {productId=BTC-USD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch candles for a specific product from Coinbase API and send them to OpenAI for prediction';

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

                $this->sendToOpenAI($candles);
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

    /**
     * Send the candle data to OpenAI for prediction.
     *
     * @param array $candles
     */
    private function sendToOpenAI(array $candles): void
    {
        try {
            $openai = new OpenAIClient(env('OPENAI_API_KEY'));

            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are a financial assistant that predicts short-term Bitcoin price movements. Use the provided historical price data to make predictions.'
                ],
                [
                    'role' => 'user',
                    'content' => "Based on the historical price data I'm going to provide, give me a JSON response with your opinion on whether Bitcoin will go up or down. " .
                                'Include the confidence percentage for different time intervals, e.g., 10m, 30m, 1h, 3h, 6h, 12h, 1d, 3d, 1w, etc., and also predict the percentage increase or decrease for the current day. ' .
                                'Here are the past Prices by hour: ' . json_encode($candles)
                ]
            ];

            $response = $openai->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => $messages
            ]);

            Log::info('OpenAI Prediction Response:', ['response' => $response]);
            $this->info('Prediction data received from OpenAI!');
        } catch (\Exception $e) {
            Log::error('Error sending to OpenAI:', ['message' => $e->getMessage()]);
            $this->error('An error occurred while sending data to OpenAI.');
        }
    }
}
