<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentRequest;
use App\Models\Investment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    // Fetch all investments
    public function index(): JsonResponse
    {
        $investments = Investment::with('user')->get();
        return response()->json($investments);
    }

    // Store a new investment
    public function store(StoreInvestmentRequest $request): JsonResponse
    {
//        $validated = $request->validate([
//            'user_id' => 'required|exists:users,id',
//            'percentage' => 'required|numeric|min:0|max:100',
//        ]);

        $investment = Investment::create($request->validated());

        return response()->json(['message' => 'Investment created successfully.', 'data' => $investment], 201);
    }

    // Show a specific investment
    public function show($id): JsonResponse
    {
        $investment = Investment::with('user')->findOrFail($id);
        return response()->json($investment);
    }

    // Update an existing investment
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $investment = Investment::findOrFail($id);
        $investment->update($validated);

        return response()->json(['message' => 'Investment updated successfully.', 'data' => $investment]);
    }

    // Delete an investment
    public function destroy($id): JsonResponse
    {
        $investment = Investment::findOrFail($id);
        $investment->delete();

        return response()->json(['message' => 'Investment deleted successfully.']);
    }
}
