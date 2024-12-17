<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @method static findOrFail($id)
 * @method static create(array $validated)
 * @property int $id
 * @property int $user_id
 * @property string $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Investment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereUserId($value)
 * @mixin \Eloquent
 */
class Investment extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
