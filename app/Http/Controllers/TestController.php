<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TestController extends Controller
{
    private function generateToken($path, $method = 'GET'): string
    {
        $baseUrl = env('COINBASE_BASE_URL'); // Ensure this is defined in your .env
        $keyName = env('COINBASE_KEY_NAME');
        $keySecret = env('COINBASE_KEY_SECRET');
        $uri = $method . ' ' . $baseUrl . $path;

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

    private function apiCall(string $path, array $params = [], string $method = 'GET', $data = null): array
    {
        $baseUrl = env('COINBASE_BASE_URL'); // Define in your .env
        $keyName = env('COINBASE_KEY_NAME');
        $token = $this->generateToken($path, $method);

        $queryString = http_build_query($params);
        $url = $baseUrl . $path . ($queryString ? '?' . $queryString : '');

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'CB-ACCESS-KEY' => $keyName,
            'CB-ACCESS-SIGN' => $token,
            'CB-ACCESS-TIMESTAMP' => time(),
            'Content-Type' => 'application/json',
        ];

        try {
            $client = new Client();
            $response = $client->request($method, $url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return json_decode($response->getBody()->getContents(), true);
            }
            return ['error' => $e->getMessage()];
        }
    }

    public function test(): JsonResponse
    {
        $path = '/accounts'; // Example path
        $params = [
            'limit' => 100, // Example parameter
        ];

        $response = $this->apiCall($path, $params);

        return response()->json([
            'state' => 'success',
            'message' => 'This is a test route',
            'data' => $response,
        ]);
    }
}
