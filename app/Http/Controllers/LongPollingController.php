<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LongPollingController extends Controller
{
    protected OrderController $orderController;

    public function __construct(OrderController $orderController)
    {
        $this->orderController = $orderController;
    }

    public function getDataLongPolling(Request $request): JsonResponse
    {
        try {
            $data = array_merge([
                'accountOrders' => $this->orderController->getAccountOrders($request),
                'accountInfo' => $this->orderController->getAccountInfo($request),
                'coinbaseState' => $this->orderController->getState(),
            ]);

            Log::info('Long polling data retrieved successfully');

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve long polling data', [
                'message' => $e->getMessage(),
                'request_id' => $request->header('X-Request-ID')
            ]);

            return response()->json([
                'error' => 'Failed to retrieve data'
            ], 500);
        }
    }
}



//
//{
//    "uuid": "ee78eddd-ea57-5fee-8228-627d0d7acbb0",
//    "name": "BTC Wallet",
//    "currency": "BTC",
//    "available_balance": {
//    "value": "0.0001672322761103",
//        "currency": "BTC"
//    },
//    "default": true,
//    "active": true,
//    "created_at": "2015-12-07T23:54:50.163Z",
//    "updated_at": "2024-11-12T08:41:52.681Z",
//    "deleted_at": null,
//    "type": "ACCOUNT_TYPE_CRYPTO",
//    "ready": true,
//    "hold": {
//    "value": "0.00074863",
//        "currency": "BTC"
//    },
//    "retail_portfolio_id": "6350fd72-8199-550b-a1c6-0292f9be55bc",
//    "orders": [
//]
//},

