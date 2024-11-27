<?php

namespace App\Http\Controllers;

use App\Models\InvestmentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class InvestmentTransactionController extends Controller
{
    // Fetch all transactions
    public function index(): JsonResponse
    {
        $transactions = InvestmentTransaction::with('user')->get();
        return response()->json($transactions);
    }

    // Store a new transaction
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'transaction_type' => 'required|in:investment,withdrawal',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $transaction = InvestmentTransaction::create($validated);

        return response()->json(['message' => 'Transaction created successfully.', 'data' => $transaction], 201);
    }

    // Fetch transactions by user
    public function getByUser($userId): JsonResponse
    {
        $transactions = InvestmentTransaction::where('user_id', $userId)->get();
        return response()->json($transactions);
    }
}
