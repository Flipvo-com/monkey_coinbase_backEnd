<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Deposit Function
    public function deposit(Request $request)
    {
        $data = $request->validate([
            'currency_type' => 'required|in:crypto,fiat',
            'crypto_symbol' => 'nullable|string',
            'fiat_currency' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|in:bank_transfer,credit_card,cash',
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'deposit',
            'currency_type' => $data['currency_type'],
            'crypto_symbol' => $data['crypto_symbol'] ?? null,
            'fiat_currency' => $data['fiat_currency'] ?? null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['transaction' => $transaction], 201);
    }

    // Withdrawal Function
    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'currency_type' => 'required|in:crypto,fiat',
            'crypto_symbol' => 'nullable|string',
            'fiat_currency' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|in:bank_transfer,cash,paypal',
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'currency_type' => $data['currency_type'],
            'crypto_symbol' => $data['crypto_symbol'] ?? null,
            'fiat_currency' => $data['fiat_currency'] ?? null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['transaction' => $transaction], 201);
    }

    // View User Transactions
    public function getUserTransactions()
    {
        $transactions = Transaction::where('user_id', auth()->id())->get();
        return response()->json(['transactions' => $transactions]);
    }
}
