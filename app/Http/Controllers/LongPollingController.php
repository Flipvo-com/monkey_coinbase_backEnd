<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;

class LongPollingController extends Controller
{
    public function getDataLongPolling(Request $request): JsonResponse
    {
        // get data from OrderController and return it
        $data['accountOrders'] = (new OrderController)->getAccountOrders($request);
        $data['accountInfo'] = (new OrderController)->getAccountInfo($request);
        // add more data to the response

        sleep(10);
        return response()->json($data);
    }
}
