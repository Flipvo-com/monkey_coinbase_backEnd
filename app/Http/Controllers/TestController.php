<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private const BASE_URL = 'https://api.coinbase.com/api/v3/brokerage'; // Stored BASE_URL directly here

    public function test(Request $request): JsonResponse
    {

        $path = '/accounts'; // Example API endpoint
        $params = [
            'limit' => 100, // Example parameter
        ];

        $response = $this->apiCall($path, $params);

        if (isset($response['error'])) {
            return response()->json([
                'state' => 'error',
                'message' => 'API call failed',
                'data' => $response,
            ]);
        }

        return response()->json([
            'state' => 'success',
            'message' => 'API call succeeded',
            'data' => $response,
        ]);
    }

    private function apiCall(string $path, array $params = [], string $method = 'GET', $data = null): array
    {
        $keyName = env('COINBASE_KEY_NAME');
        $token = $this->generateToken($path, $method);

        $queryString = http_build_query($params);
        $url = self::BASE_URL . $path . ($queryString ? '?' . $queryString : '');

//        return [
//            'url' => $url,
//            'token' => $token,
//            'keyName' => $keyName,
//            'method' => $method,
//            'data' => $data,
//        ];


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
        $uri = $method . ' ' . self::BASE_URL . $path;

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
}
