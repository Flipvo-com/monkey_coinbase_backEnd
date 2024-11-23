<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TraderController extends Controller
{
    public function __construct()
    {
    }

//    public function getState(): object
//    {
//        // Get data from JSON file and decode it as an object
//        $jsonString = file_get_contents(base_path('/resources/json/state.json'));
//
//        Log::log('info', 'State retrieved successfully');
//        Log::log('info', $jsonString);
//
//        // false returns as an object
////        return json_decode($jsonString, false);
//        return response()->json($data);
//
//    }

    public function getState(): JsonResponse
    {
        try {
            // Read the JSON file
            $filePath = base_path('/resources/json/state.json');
            if (!file_exists($filePath)) {
                Log::error('State file not found: ' . $filePath);
                return response()->json(['error' => 'State file not found'], 404);
            }

            $jsonString = file_get_contents($filePath);

            // Decode JSON into an associative array
            $data = json_decode($jsonString, true);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error decoding JSON: ' . json_last_error_msg());
                return response()->json(['error' => 'Invalid JSON format'], 500);
            }

            Log::info('State retrieved successfully', ['data' => $data]);

            // Return JSON response
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error retrieving state: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

}
