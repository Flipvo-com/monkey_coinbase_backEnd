<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class TestController extends Controller
{
    private const BASE_URL = 'https://api.coinbase.com/api/v3/brokerage';

    public function test(Request $request): JsonResponse
    {
        // Call getServerTime function
        $response = $this->getPublicProduct($request);

        return response()->json([
            'state' => 'success',
            'message' => 'API call succeeded',
            'data' => $response,
        ]);
    }

    private function apiCall(string $path, array $params = [], string $method = 'GET', $data = null): array
    {
        $keyName = env('COINBASE_KEY_NAME');
        $keySecret = env('COINBASE_KEY_SECRET');

        $token = $this->generateToken($path, $method);

        $queryString = http_build_query($params);
        $url = self::BASE_URL . $path . ($queryString ? '?' . $queryString : '');

        $timestamp = time();
        $what = $timestamp . $method . $path . ($data ? json_encode($data) : '');
        $cbAccessSign = hash_hmac('sha256', $what, $keySecret);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'CB-ACCESS-KEY' => $keyName,
            'CB-ACCESS-SIGN' => $cbAccessSign,
            'CB-ACCESS-TIMESTAMP' => (string)$timestamp,
            'Content-Type' => 'application/json',
        ];

        try {
            $client = new Client();
            $response = $client->request($method, $url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return json_decode($response->getBody()->getContents(), true) ?? [
                    'error' => $response->getReasonPhrase(),
                    'code' => $response->getStatusCode(),
                ];
            }
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    private function generateToken($path, $method = 'GET'): string
    {
        $keyName = env('COINBASE_KEY_NAME');
        $keySecret = env('COINBASE_KEY_SECRET');

        // Adjusted URI to match Node.js
        $uri = $method . ' ' . $path;

        $payload = [
            'iss' => 'cdp',
            'nbf' => time(),
            'exp' => time() + 120,
            'sub' => $keyName,
            'uri' => $uri,
        ];

        $header = [
            'kid' => $keyName,
            'nonce' => bin2hex(random_bytes(16)),
        ];

        return JWT::encode($payload, $keySecret, 'ES256', null, $header);
    }

    public function getCandlesData(string $productId = 'BTC-USD'): JsonResponse
    {
        // Prepare URL with product ID
        $url = "https://api.exchange.coinbase.com/products/$productId/candles";

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                // Add more headers if required, like authorization headers
            ),
        ));

        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'state' => 'error',
                'message' => 'cURL Error: ' . $error_msg,
            ], 500);
        }

        // Close cURL
        curl_close($curl);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle response error cases
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'state' => 'error',
                'message' => 'Failed to decode JSON response',
                'data' => $response,
            ], 500);
        }

        // Return the response data in JSON format
        return response()->json([
            'state' => 'success',
            'message' => 'API call succeeded',
            'data' => $responseData,
        ]);
    }

    // Function to get server time

    public function getServerTime(): JsonResponse
    {
        return $this->makeGetRequest('/time');
    }

    // Function to get public product book

    private function makeGetRequest(string $path): JsonResponse
    {
        $url = self::BASE_URL . $path;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'state' => 'error',
                'message' => 'cURL Error: ' . $error_msg,
            ], 500);
        }

        curl_close($curl);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'state' => 'error',
                'message' => 'Failed to decode JSON response',
                'data' => $response,
            ], 500);
        }

        return response()->json([
            'state' => 'success',
            'message' => 'API call succeeded',
            'data' => $responseData,
        ]);
    }

    // Function to list public products

    public function getPublicProductBook(): JsonResponse
    {
        return $this->makeGetRequest('/market/product_book');
    }

    // Function to get details of a specific product

    public function listPublicProducts(): JsonResponse
    {
        return $this->makeGetRequest('/market/products');
    }

    // Function to get public product candles

//    public function getPublicProduct(Request $request): JsonResponse
//    {
//        $productId = $request->get('product_id', 'BTC-USD');
//        return $this->makeGetRequest("/market/products/$productId");
//    }

    public function getPublicProduct(Request $request): JsonResponse
    {
        $productId = $request->get('product_id', 'BTC-USD');

        // Cache key
        $cacheKey = "market_product_{$productId}";

        // Attempt to fetch from cache, or execute and cache the result for 30 seconds
        return Cache::remember($cacheKey, 30, function () use ($productId) {
            Log::info("Fetching product data for $productId from API");
            return $this->makeGetRequest("/market/products/$productId");
        });
    }


    // Function to get public market trades for a specific product

    public function getPublicProductCandles(Request $request): JsonResponse
    {
        $productId = $request->get('product_id', 'BTC-USD');
        return $this->makeGetRequest("/market/products/$productId/candles");
    }

    // Reusable function to make GET requests using cURL

    public function getPublicMarketTrades(Request $request): JsonResponse
    {
        $productId = $request->get('product_id', 'BTC-USD');
        return $this->makeGetRequest("/market/products/$productId/ticker");
    }

}

