<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @method static where(string $string, $userId)
 * @method static create(array $validated)
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property string $transaction_type
 * @property string $transaction_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class InvestmentTransaction extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
