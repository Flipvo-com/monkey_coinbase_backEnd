<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'currency_type',
        'crypto_symbol',
        'fiat_currency',
        'amount',
        'fee',
        'tx_id',
        'status',
        'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
