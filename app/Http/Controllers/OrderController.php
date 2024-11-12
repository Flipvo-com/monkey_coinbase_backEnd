<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function getState(): object
    {
        // Get data from JSON file and decode it as an object
        $jsonString = file_get_contents(base_path('resources/json/state.json'));
        Log::log('info', 'State retrieved successfully');
        Log::log('info', $jsonString);

        // false returns as an object
        return json_decode($jsonString, false);
    }


    public function getAccountOrders(Request $request): array
    {
        // get data from JSON file and return it
        $jsonString = file_get_contents(base_path('resources/json/state.json'));
        $data = collect(json_decode($jsonString));

        // from this data collection get the accounts->orders where the accounts->currency == $request->currency
        // make the data collection contain object not array
        // Extract accounts and filter orders based on currency
        $orders = collect($data->get('accounts'))
            ->flatten(1)
            ->filter(function ($account) use ($request) {
                return isset($account->currency) && $account->currency == $request->accountCurrency;
            })
            ->pluck('orders')
            ->flatten(1);
        // Separate 10 "SELL" and 10 "BUY" orders
        $sellOrders = $orders->where('side', 'SELL')->take(10);
        $buyOrders = $orders->where('side', 'BUY')->take(10);

        return $sellOrders->merge($buyOrders)->toArray();
    }

    public function getAccountInfo(Request $request): array
    {
        // get data from JSON file and return it
        $jsonString = file_get_contents(base_path('resources/json/state.json'));
        $data = collect(json_decode($jsonString));

        // from this data collection get the accounts where the accounts->currency == $request->currency
        // make the data collection contain object not array
        // Extract accounts and get all accounts data except orders
        $account = collect($data->get('accounts'))
            ->flatten(1)
            ->filter(function ($account) use ($request) {
                return isset($account->currency) && $account->currency == $request->accountCurrency;
            })
            ->map(function ($account) {
                return collect($account)->except('orders');
            })
            ->first();
        return $account->toArray();
    }

//    public function getAccountOrders(Request $request): JsonResponse
//    {
//        // get data from OrderController and return it
//        $data['accountOrders'] = (new OrderController)->getAccountOrders($request);
//        $data['accountInfo'] = (new OrderController)->getAccountInfo($request);
//        // add more data to the response
//
//        sleep(10);
//        return response()->json($data);
//    }
}

